<?php
include('./config/db.php');
include('./includes/header.php');

// Fetch customers
$customers = $conn->query("SELECT id, name FROM customers ORDER BY name");
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mt-4 mb-3">
    <h3 class="fw-bold text-primary mb-0">
        📏 Add Measurement
    </h3>
    <a href="measurements.php" class="btn btn-outline-secondary">
        ← Back to List
    </a>
</div>

<!-- Form Card -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body">
        <form method="post" id="measurementForm" class="needs-validation" novalidate>

            <!-- Customer Selection -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Customer</label>
                <select name="customer_id" class="form-control" required>
                    <option value="">-- Select Customer --</option>
                    <?php while ($row = $customers->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Garment Type -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Garment Type</label>
                <select name="garment_type" id="garment_type" class="form-control" required>
                    <option value="">-- Select Garment --</option>
                    <option value="Shirt">Shirt</option>
                    <option value="Pant">Pant</option>
                    <option value="Kurta">Kurta</option>
                    <option value="Coat">Coat</option>
                    <option value="Blouse">Blouse</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <!-- Dynamic Measurement Fields -->
            <div id="measurementFields" class="mb-3"></div>

            <!-- Date Taken -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Date Taken</label>
                <input type="date" name="date_taken" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex justify-content-end">
                <button type="submit" name="save" class="btn btn-success px-4 me-2">
                    💾 Save Measurement
                </button>
                <a href="measurements.php" class="btn btn-secondary px-4">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Dynamic Measurement JS -->
<script>
const templates = {
    "Shirt": ["Chest", "Waist", "Shoulder", "Sleeve", "Length", "Collar"],
    "Pant": ["Waist", "Hip", "Thigh", "Length", "Knee", "Bottom"],
    "Kurta": ["Chest", "Waist", "Shoulder", "Sleeve", "Length", "Neck"],
    "Coat": ["Chest", "Waist", "Shoulder", "Sleeve", "Length", "Arm Hole"],
    "Blouse": ["Bust", "Waist", "Shoulder", "Sleeve", "Length", "Neck"],
    "Other": []
};

document.getElementById('garment_type').addEventListener('change', function() {
    const type = this.value;
    const container = document.getElementById('measurementFields');
    container.innerHTML = '';

    let html = '<label class="form-label fw-semibold">Measurements (in inches)</label>';
    html += '<div id="measurementInputs" class="row">';
    
    if (templates[type]) {
        templates[type].forEach(field => {
            html += `
            <div class="col-md-4 mb-2">
                <label>${field}</label>
                <input type="text" name="measurements[${field}]" class="form-control" placeholder="Enter ${field}">
            </div>`;
        });
    }

    html += '</div>';
    html += `
        <div class="mt-3">
            <button type="button" id="addCustomField" class="btn btn-sm btn-outline-primary">
                ➕ Add Custom Field
            </button>
        </div>
    `;

    container.innerHTML = html;

    // Add event listener for custom fields
    document.getElementById('addCustomField').addEventListener('click', function() {
        const customFieldHTML = `
            <div class="col-md-4 mb-2 custom-field">
                <input type="text" class="form-control mb-1" name="custom_field_name[]" placeholder="Custom Field Name" required>
                <input type="text" class="form-control" name="custom_field_value[]" placeholder="Value">
            </div>
        `;
        document.getElementById('measurementInputs').insertAdjacentHTML('beforeend', customFieldHTML);
    });
});
</script>

<?php
// Handle form submission
if (isset($_POST['save'])) {
    $cid = intval($_POST['customer_id']);
    $garment = $_POST['garment_type'];
    $date = $_POST['date_taken'];

    // Default measurements
    $measurements = isset($_POST['measurements']) ? $_POST['measurements'] : [];

    // Handle custom fields
    if (isset($_POST['custom_field_name']) && isset($_POST['custom_field_value'])) {
        foreach ($_POST['custom_field_name'] as $index => $field_name) {
            $field_name = trim($field_name);
            $field_value = $_POST['custom_field_value'][$index] ?? '';
            if (!empty($field_name)) {
                $measurements[$field_name] = $field_value;
            }
        }
    }

    // Convert to JSON
    $data_json = json_encode($measurements, JSON_UNESCAPED_UNICODE);

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO measurements (customer_id, garment_type, data, date_taken) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $cid, $garment, $data_json, $date);

    if ($stmt->execute()) {
        echo "<script>alert('Measurement saved successfully');window.location='measurements.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}
?>

<!-- Styling -->
<style>
    .card {
        transition: box-shadow 0.2s ease-in-out;
    }
    .card:hover {
        box-shadow: 0 0 12px rgba(0,0,0,0.08);
    }
    input.form-control, select.form-control {
        border-radius: 8px;
    }
    label {
        font-weight: 500;
    }
    .custom-field input {
        border-color: #007bff40;
    }
</style>

<?php include('./includes/footer.php'); ?>
