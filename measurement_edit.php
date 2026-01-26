<?php
include('./config/db.php');
include('./includes/header.php');

// =========================================
// 1️⃣ Validate ID
// =========================================
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid Access'); window.location='measurements.php';</script>";
    exit;
}

$id = intval($_GET['id']);
$res = $conn->query("SELECT * FROM measurements WHERE id = $id");

if ($res->num_rows == 0) {
    echo "<script>alert('Measurement not found'); window.location='measurements.php';</script>";
    exit;
}

$row = $res->fetch_assoc();

// Decode old measurement data
$data = @unserialize($row['data']);
if ($data === false) $data = json_decode($row['data'], true);
if (!is_array($data)) $data = [];

// =========================================
// 2️⃣ UPDATE LOGIC + SAVE HISTORY
// =========================================
if (isset($_POST['update'])) {

    // Save old data to history BEFORE update
    $oldDataJSON = $row['data'];

    $stmtH = $conn->prepare("
        INSERT INTO measurement_history
        (measurement_id, customer_id, garment_type_id, subtype_id, data, date_taken)
        VALUES (?,?,?,?,?,?)
    ");
    $stmtH->bind_param(
        "iiiiss",
        $id,
        $row['customer_id'],
        $row['garment_type_id'],
        $row['subtype_id'],
        $oldDataJSON,
        $row['date_taken']
    );
    $stmtH->execute();


    // -----------------------------
    // Now Update Current Record
    // -----------------------------
    $cid   = intval($_POST['customer_id']);
    $type  = intval($_POST['garment_type_id']);
    $sub   = intval($_POST['subtype_id']);
    $date  = $_POST['date_taken'];
    $meas  = $_POST['measurements'] ?? [];

    // Add custom fields
    if (!empty($_POST['custom_field_name'])) {
        foreach ($_POST['custom_field_name'] as $i => $name) {
            $name = trim($name);
            $value = $_POST['custom_field_value'][$i] ?? '';
            if ($name !== "") {
                $meas[$name] = $value;
            }
        }
    }

    $json = json_encode($meas, JSON_UNESCAPED_UNICODE);

    $stmt = $conn->prepare("
        UPDATE measurements 
        SET customer_id=?, garment_type_id=?, subtype_id=?, data=?, date_taken=? 
        WHERE id=?");
    $stmt->bind_param("iiissi", $cid, $type, $sub, $json, $date, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Measurement updated successfully'); window.location='measurements.php';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger'>Error: {$stmt->error}</div>";
    }
}
?>

<!-- =========================================
3️⃣ PAGE HEADER
========================================= -->

<div class="d-flex justify-content-between align-items-center mt-4 mb-3" style="padding-top:0.5rem;">
    <h3 class="fw-bold text-primary mb-0">✂️ Edit Measurement</h3>

    <div>
        <a href="measurement_history.php?id=<?= $id ?>" class="btn btn-info">📜 View History</a>
        <a href="measurements.php" class="btn btn-outline-secondary">← Back</a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body">
        <form method="post" id="measurementForm">

            <!-- Customer -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Customer</label>
                <select name="customer_id" class="form-control" required>
                    <option value="">Select Customer</option>
                    <?php
                    $customers = $conn->query("SELECT id, name FROM customers ORDER BY name");
                    while ($c = $customers->fetch_assoc()):
                        $sel = ($c['id'] == $row['customer_id']) ? "selected" : "";
                    ?>
                        <option value="<?= $c['id'] ?>" <?= $sel ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Garment Type -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Garment Type</label>
                <select name="garment_type_id" id="garment_type_id" class="form-control" required>
                    <option value="">Select Type</option>
                    <?php
                    $types = $conn->query("SELECT id, title FROM cloth_types ORDER BY title");
                    while ($t = $types->fetch_assoc()):
                        $sel = ($t['id'] == $row['garment_type_id']) ? "selected" : "";
                    ?>
                        <option value="<?= $t['id'] ?>" <?= $sel ?>><?= htmlspecialchars($t['title']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Subtype -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Sub Type</label>
                <select name="subtype_id" id="subtype_id" class="form-control">
                    <option value="">Select Subtype</option>
                    <?php
                    $subs = $conn->query("SELECT id, title FROM cloth_subtypes WHERE cloth_type_id = {$row['garment_type_id']} ORDER BY title");
                    while ($s = $subs->fetch_assoc()):
                        $sel = ($s['id'] == $row['subtype_id']) ? "selected" : "";
                    ?>
                        <option value="<?= $s['id'] ?>" <?= $sel ?>><?= htmlspecialchars($s['title']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Measurement Fields -->
            <div id="measurementFields" class="mb-3"></div>

            <!-- Date -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Date Taken</label>
                <input type="date" name="date_taken" value="<?= $row['date_taken'] ?>" class="form-control">
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" name="update" class="btn btn-success px-4 me-2">💾 Update</button>
                <a href="measurements.php" class="btn btn-secondary px-4">Cancel</a>
            </div>
        </form>
    </div>
</div>

<!-- =========================================
4️⃣ JAVASCRIPT
========================================= -->

<script>
const existingData = <?php echo json_encode($data, JSON_UNESCAPED_UNICODE); ?>;

function loadFields() {
  const container = document.getElementById('measurementFields');
  container.innerHTML = '';

  let html = '<label class="form-label fw-semibold mb-2">Measurements (in inches)</label>';
  html += '<div id="measurementInputs" class="row">';

  if (Object.keys(existingData).length > 0) {
      Object.entries(existingData).forEach(([key, value]) => {
          html += `
              <div class="col-md-4 mb-3">
                  <label class="small text-muted">${key}</label>
                  <input type="text" name="measurements[${key}]" value="${value}"
                         class="form-control form-control-sm" placeholder="Enter ${key}">
              </div>`;
      });
  } else {
      html += '<div class="col-12 text-muted text-center">No measurements available.</div>';
  }

  html += '</div>';
  html += `
      <div class="mt-2">
          <button type="button" id="addCustomField" class="btn btn-sm btn-outline-primary">
              ➕ Add Custom Field
          </button>
      </div>`;

  container.innerHTML = html;

  document.getElementById('addCustomField').addEventListener('click', function() {
      const newField = `
          <div class="col-md-4 mb-3 custom-field">
              <input type="text" class="form-control mb-1 form-control-sm" name="custom_field_name[]" placeholder="Custom Field Name">
              <input type="text" class="form-control form-control-sm" name="custom_field_value[]" placeholder="Value">
          </div>`;
      document.getElementById('measurementInputs').insertAdjacentHTML('beforeend', newField);
  });
}

loadFields();

// Load subtypes dynamically
document.getElementById('garment_type_id').addEventListener('change', function() {
  const typeId = this.value;
  fetch('get_subtypes.php?type_id=' + typeId)
    .then(res => res.json())
    .then(data => {
      const sub = document.getElementById('subtype_id');
      sub.innerHTML = '<option value="">Select Subtype</option>';
      data.forEach(s => {
          sub.innerHTML += `<option value="${s.id}">${s.title}</option>`;
      });
    });
});
</script>

<!-- CSS -->
<style>
.card { transition: 0.3s; }
.card:hover { box-shadow: 0 0 12px rgba(0,0,0,0.1); }
label { font-weight: 500; color: #0d6efd; }
.form-control, select { border-radius: 6px; }
.btn { font-weight: 500; }
</style>

<?php include('./includes/footer.php'); ?>
