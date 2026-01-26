<?php
include('./includes/header.php');
include('./config/db.php');

// Fetch customers
$customers = $conn->query("SELECT id, name FROM customers ORDER BY name");

// Handle save
if (isset($_POST['save'])) {
    $cid = intval($_POST['customer_id']);
    $cloth_type_id = intval($_POST['cloth_type_id']);
    $date_taken = $_POST['date_taken'];

    // Collect measurement data
    $measurements = [];
    if (isset($_POST['measurements'])) {
        foreach ($_POST['measurements'] as $name => $value) {
            $measurements[$name] = trim($value);
        }
    }

    // Handle custom fields
    if (isset($_POST['custom_field_name']) && isset($_POST['custom_field_value'])) {
        foreach ($_POST['custom_field_name'] as $i => $name) {
            $name = trim($name);
            $value = $_POST['custom_field_value'][$i] ?? '';
            if (!empty($name)) {
                $measurements[$name] = $value;
            }
        }
    }

    // Convert to JSON
    $data_json = json_encode($measurements, JSON_UNESCAPED_UNICODE);

    // Insert
    $stmt = $conn->prepare("INSERT INTO measurements (customer_id, cloth_type_id, data, date_taken) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $cid, $cloth_type_id, $data_json, $date_taken);

    if ($stmt->execute()) {
        echo "<script>alert('Measurement saved successfully');window.location='measurements.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
    $stmt->close();
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary mb-0">📏 Add Measurement</h3>
        <a href="measurements.php" class="btn btn-outline-secondary">← Back to List</a>
    </div>

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body">
            <form method="post" id="measurementForm">
                <!-- Customer -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Select Customer</label>
                    <select name="customer_id" class="form-control" required>
                        <option value="">-- Select Customer --</option>
                        <?php while ($c = $customers->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Cloth Type -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Cloth Type</label>
                    <select name="cloth_type_id" id="cloth_type_id" class="form-control" required>
                        <option value="">-- Select Cloth Type --</option>
                        <?php
                        $cloths = $conn->query("SELECT id, title FROM cloth_types ORDER BY title");
                        while ($r = $cloths->fetch_assoc()):
                        ?>
                            <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['title']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Measurement Fields -->
                <div id="measurementFields"></div>

                <!-- Date -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Date Taken</label>
                    <input type="date" name="date_taken" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <!-- Buttons -->
                <div class="text-end">
                    <button type="submit" name="save" class="btn btn-success px-4">💾 Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS: Fetch Measurements -->
<script>
document.getElementById('cloth_type_id').addEventListener('change', function() {
    const clothTypeId = this.value;
    const container = document.getElementById('measurementFields');
    container.innerHTML = '';

    if (clothTypeId) {
        fetch('get_measurements.php?id=' + clothTypeId)
            .then(res => res.json())
            .then(data => {
                let html = '<label class="form-label fw-semibold">Measurements (inches/cm)</label>';
                html += '<div id="measurementInputs" class="row">';
                data.forEach(m => {
                    html += `
                        <div class="col-md-4 mb-2">
                            <label>${m.field_name} (${m.unit})</label>
                            <input type="text" class="form-control" name="measurements[${m.field_name}]"
                                   placeholder="Enter ${m.field_name}">
                        </div>`;
                });
                html += '</div>';
                html += `
                    <div class="mt-3">
                        <button type="button" id="addCustomField" class="btn btn-sm btn-outline-primary">
                            ➕ Add Custom Field
                        </button>
                    </div>`;
                container.innerHTML = html;

                // Add custom field handler
                document.getElementById('addCustomField').addEventListener('click', () => {
                    const customFieldHTML = `
                        <div class="col-md-4 mb-2 custom-field">
                            <input type="text" class="form-control mb-1" name="custom_field_name[]" placeholder="Custom Field Name" required>
                            <input type="text" class="form-control" name="custom_field_value[]" placeholder="Value">
                        </div>`;
                    document.getElementById('measurementInputs').insertAdjacentHTML('beforeend', customFieldHTML);
                });
            })
            .catch(err => {
                container.innerHTML = `<div class='alert alert-warning'>Error fetching measurements!</div>`;
                console.error(err);
            });
    }
});
</script>

<style>
.card {
    transition: box-shadow 0.2s ease-in-out;
}
.card:hover {
    box-shadow: 0 0 12px rgba(0,0,0,0.08);
}
.custom-field input {
    border-color: #007bff40;
}
</style>

<?php include('./includes/footer.php'); ?>
