<?php
include('./includes/header.php');
include('./config/db.php');

// Fetch all cloth subtypes joined with cloth types for dropdown
$subtypes_sql = "SELECT cs.id as subtype_id, cs.title as subtype_title, ct.title as cloth_type_title 
                 FROM cloth_subtypes cs 
                 JOIN cloth_types ct ON cs.cloth_type_id = ct.id
                 ORDER BY ct.title, cs.title";
$cloth_subtypes = mysqli_query($conn, $subtypes_sql);

// Handle Save/Update
if (isset($_POST['save_measurements'])) {
    $subtype_id = intval($_POST['cloth_subtype']);
    $fields = $_POST['field_name'];
    $units = $_POST['unit'];
    $order = $_POST['sequence'];

    // Remove old entries for this subtype
    mysqli_query($conn, "DELETE FROM measurement_master WHERE cloth_subtype_id=$subtype_id");

    // Insert updated ones
    $stmt = $conn->prepare("INSERT INTO measurement_master (cloth_subtype_id, measurement_title, unit, sequence_order) VALUES (?, ?, ?, ?)");
    foreach ($fields as $i => $name) {
        if (trim($name) != '') {
            $unit = $units[$i] ?? '';
            $seq = intval($order[$i] ?? 0);
            $stmt->bind_param("issi", $subtype_id, $name, $unit, $seq);
            $stmt->execute();
        }
    }
    echo "<script>alert('Measurements updated successfully!');window.location='measurements_master.php';</script>";
    exit;
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary mb-0">📏 Add / Edit Measurements for Cloth Subtype</h3>
        <a href="cloth_type.php" class="btn btn-outline-secondary">🧵 Manage Cloth Types</a>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <form method="post" id="measureForm">
                <!-- Select Cloth Subtype -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Select Cloth Subtype</label>
                        <select name="cloth_subtype" id="cloth_subtype" class="form-control" required>
                            <option value="">-- Select Subtype --</option>
                            <?php while ($st = mysqli_fetch_assoc($cloth_subtypes)): ?>
                                <option value="<?= $st['subtype_id'] ?>">
                                    <?= htmlspecialchars($st['cloth_type_title']) ?> &raquo; <?= htmlspecialchars($st['subtype_title']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <!-- Measurement Fields Container -->
                <div id="measurementRows">
                    <div class="text-muted">Please select a cloth subtype to view or add measurements.</div>
                </div>

                <!-- Add Row Button -->
                <div class="mt-3 d-none" id="addRowWrapper">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="addRow">➕ Add Measurement</button>
                </div>

                <!-- Save Button -->
                <div class="text-end mt-4">
                    <button type="submit" name="save_measurements" class="btn btn-success px-4 d-none" id="saveBtn">💾 Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('cloth_subtype').addEventListener('change', function() {
    const subtypeId = this.value;
    const container = document.getElementById('measurementRows');
    const addRowWrapper = document.getElementById('addRowWrapper');
    const saveBtn = document.getElementById('saveBtn');

    // Helper to update order numbers
    function updateSequence() {
        const sequenceInputs = container.querySelectorAll('input[name="sequence[]"]');
        sequenceInputs.forEach((input, index) => {
            input.value = index + 1;
        });
    }

    if (!subtypeId) {
        container.innerHTML = '<div class="text-muted">Please select a cloth subtype to view or add measurements.</div>';
        addRowWrapper.classList.add('d-none');
        saveBtn.classList.add('d-none');
        return;
    }

    fetch('get_measurement_master.php?subtype_id=' + subtypeId)
        .then(res => res.json())
        .then(data => {
            let html = '';
            if (data.length > 0) {
                data.forEach((m, index) => {
                    const unit = m.unit && m.unit.trim() !== '' ? m.unit : 'inch';
                    html += `
                    <div class="row mb-2 measurement-row">
                        <div class="col-md-4">
                            <input type="text" name="field_name[]" class="form-control" placeholder="Measurement Name" value="${m.measurement_title}">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="unit[]" class="form-control" placeholder="Unit (e.g. inch)" value="${unit}">
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="sequence[]" class="form-control sequence" placeholder="Order" value="${index + 1}">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm removeRow">🗑️</button>
                        </div>
                    </div>`;
                });
            } else {
                html = `
                <div class="row mb-2 measurement-row">
                    <div class="col-md-4">
                        <input type="text" name="field_name[]" class="form-control" placeholder="Measurement Name">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="unit[]" class="form-control" placeholder="Unit (e.g. inch)" value="inch">
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="sequence[]" class="form-control sequence" placeholder="Order" value="1">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm removeRow">🗑️</button>
                    </div>
                </div>`;
            }

            container.innerHTML = html;
            addRowWrapper.classList.remove('d-none');
            saveBtn.classList.remove('d-none');

            updateSequence(); // ensure correct order numbers
        })
        .catch(err => {
            console.error(err);
            container.innerHTML = '<div class="text-danger">Error loading measurements.</div>';
        });
});


// ✅ Add New Measurement Row (auto sequence + default inch)
document.getElementById('addRow').addEventListener('click', function() {
    const container = document.getElementById('measurementRows');
    const nextOrder = container.querySelectorAll('.measurement-row').length + 1;

    const html = `
    <div class="row mb-2 measurement-row">
        <div class="col-md-4">
            <input type="text" name="field_name[]" class="form-control" placeholder="Measurement Name">
        </div>
        <div class="col-md-3">
            <input type="text" name="unit[]" class="form-control" placeholder="Unit (e.g. inch)" value="inch">
        </div>
        <div class="col-md-3">
            <input type="number" name="sequence[]" class="form-control sequence" placeholder="Order" value="${nextOrder}">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm removeRow">🗑️</button>
        </div>
    </div>`;
    container.insertAdjacentHTML('beforeend', html);
    updateSequence();
});

// ✅ Remove Row and Reorder Automatically
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('removeRow')) {
        const row = e.target.closest('.measurement-row');
        row.remove();
        updateSequence();
    }
});

// ✅ Helper: keeps order updated globally
function updateSequence() {
    const container = document.getElementById('measurementRows');
    const sequenceInputs = container.querySelectorAll('input[name="sequence[]"]');
    sequenceInputs.forEach((input, index) => {
        input.value = index + 1;
    });
}
</script>


<style>
.card {
    border-radius: 10px;
}
</style>

<?php include('./includes/footer.php'); ?>
