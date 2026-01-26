<?php 
include('./config/db.php'); 
include('./includes/header.php'); 

// Fetch customers
$customers = $conn->query("SELECT id, name FROM customers ORDER BY name");

// Fetch cloth types
$cloth_types = $conn->query("SELECT id, title FROM cloth_types ORDER BY title");

$editData = null;

// If user selects customer + cloth type + subtype, we’ll later fetch existing measurement (via JS or post-back)
?>

<h3 class="fw-bold text-primary mt-4 mb-3">📏 Add / Edit Measurement</h3>

<div class="card shadow-sm border-0 rounded-3 mb-4">
  <div class="card-body">
    <form method="post" id="measurementForm" enctype="multipart/form-data">
      
      <!-- Customer -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Customer</label>
        <select name="customer_id" class="form-control" id="customer_id" required>
          <option value="">-- Select Customer --</option>
          <?php while ($row = $customers->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Cloth Type -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Cloth Type</label>
        <select name="garment_type" id="garment_type" class="form-control" required>
          <option value="">-- Select Cloth Type --</option>
          <?php while ($row = $cloth_types->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Sub Category -->
      <div class="mb-3" id="subtypeContainer" style="display:none;">
        <label class="form-label fw-semibold">Sub Category</label>
        <select name="subtype_id" id="subtype_id" class="form-control">
          <option value="">-- Select Sub Category --</option>
        </select>
      </div>

      <!-- Subtype Image -->
      <div class="mb-3 text-center" id="subtypeImageContainer" style="display:none;">
        <img id="subtypeImage" src="" alt="Subtype Image" class="img-thumbnail shadow-sm" width="180">
      </div>

      <!-- Measurements -->
      <div id="measurementFields" class="mb-3"></div>

      <!-- Date Taken -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Date Taken</label>
        <input type="date" name="date_taken" class="form-control" value="<?= date('Y-m-d') ?>" required>
      </div>

      <input type="hidden" name="measurement_id" id="measurement_id">

      <div class="d-flex justify-content-end">
        <button type="submit" name="save" class="btn btn-success px-4 me-2">💾 Save</button>
        <a href="measurements.php" class="btn btn-secondary px-4">Cancel</a>
      </div>
    </form>
  </div>
</div>

<script>
// Load Subtypes
document.getElementById('garment_type').addEventListener('change', function() {
  const typeId = this.value;
  const subtypeContainer = document.getElementById('subtypeContainer');
  const subtypeSelect = document.getElementById('subtype_id');
  const imageContainer = document.getElementById('subtypeImageContainer');
  const measurementContainer = document.getElementById('measurementFields');

  subtypeContainer.style.display = 'none';
  imageContainer.style.display = 'none';
  measurementContainer.innerHTML = '';

  if (!typeId) return;

  fetch('get_subtypes.php?type_id=' + typeId)
    .then(res => res.json())
    .then(subtypes => {
      if (subtypes.length > 0) {
        subtypeContainer.style.display = 'block';
        subtypeSelect.innerHTML = '<option value="">-- Select Sub Category --</option>';
        subtypes.forEach(s => {
          subtypeSelect.innerHTML += `<option data-image="${s.image}" value="${s.id}">${s.title}</option>`;
        });
      }
    });
});

// Load measurements + existing data if any
document.getElementById('subtype_id').addEventListener('change', async function() {
  const subtypeId = this.value;
  const selected = this.options[this.selectedIndex];
  const imgSrc = selected.dataset.image ? 'uploads/subtypes/' + selected.dataset.image : '';
  const container = document.getElementById('subtypeImageContainer');
  const img = document.getElementById('subtypeImage');
  const measurementContainer = document.getElementById('measurementFields');
  const customerId = document.getElementById('customer_id').value;
  const clothId = document.getElementById('garment_type').value;
  const measurementIdField = document.getElementById('measurement_id');

  measurementIdField.value = '';
  if (imgSrc) {
    img.src = imgSrc;
    container.style.display = 'block';
  } else {
    container.style.display = 'none';
  }

  if (!subtypeId || !customerId || !clothId) {
    measurementContainer.innerHTML = '';
    return;
  }

  // Check if a measurement already exists
  const checkRes = await fetch(`check_measurement.php?customer_id=${customerId}&cloth_id=${clothId}&subtype_id=${subtypeId}`);
  const existing = await checkRes.json();

  // Get master fields
  const masterRes = await fetch('get_measurement_master.php?subtype_id=' + subtypeId);
  const fields = await masterRes.json();

  let html = `<label class="form-label fw-semibold">Measurements (in inches)</label>
              <div id="measurementInputs" class="row">`;

  if (fields.length > 0) {
    fields.forEach(f => {
      const val = existing.data && existing.data[f.measurement_title] ? existing.data[f.measurement_title] : '';
      html += `
        <div class="col-md-4 mb-2">
          <label>${f.measurement_title} (${f.unit || ''})</label>
          <input type="text" name="measurements[${f.measurement_title}]" class="form-control" value="${val}">
        </div>`;
    });
  } else {
    html += `<div class="col-12 text-muted">No default measurements.</div>`;
  }

  html += `</div>
           <div class="mt-3">
             <button type="button" id="addCustomField" class="btn btn-sm btn-outline-primary">➕ Add Custom Field</button>
           </div>`;

  measurementContainer.innerHTML = html;
  if (existing.id) measurementIdField.value = existing.id;

  document.getElementById('addCustomField').addEventListener('click', function() {
    const custom = `
      <div class="col-md-4 mb-2 custom-field">
        <input type="text" class="form-control mb-1" name="custom_field_name[]" placeholder="Custom Field Name">
        <input type="text" class="form-control" name="custom_field_value[]" placeholder="Value">
      </div>`;
    document.getElementById('measurementInputs').insertAdjacentHTML('beforeend', custom);
  });
});
</script>

<?php
// Handle Save / Update
if (isset($_POST['save'])) {
    $cid = intval($_POST['customer_id']);
    $cloth_id = intval($_POST['garment_type']);
    $subtype_id = $_POST['subtype_id'] !== '' ? intval($_POST['subtype_id']) : null;
    $date = $_POST['date_taken'];
    $measurements = $_POST['measurements'] ?? [];
    $id = intval($_POST['measurement_id'] ?? 0);

    if (!empty($_POST['custom_field_name'])) {
        foreach ($_POST['custom_field_name'] as $i => $name) {
            if (trim($name) !== '') {
                $measurements[$name] = $_POST['custom_field_value'][$i] ?? '';
            }
        }
    }

    $data_json = json_encode($measurements, JSON_UNESCAPED_UNICODE);

    if ($id > 0) {
        // Update existing
        $stmt = $conn->prepare("UPDATE measurements SET data=?, date_taken=? WHERE id=?");
        $stmt->bind_param("ssi", $data_json, $date, $id);
        $stmt->execute();
        echo "<script>alert('Measurement updated successfully');window.location='measurements.php';</script>";
    } else {
        // Insert new
        $stmt = $conn->prepare("INSERT INTO measurements (customer_id, garment_type_id, subtype_id, data, date_taken) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $cid, $cloth_id, $subtype_id, $data_json, $date);
        $stmt->execute();
        echo "<script>alert('Measurement saved successfully');window.location='measurements.php';</script>";
    }
    $stmt->close();
}

include('./includes/footer.php'); 
?>
