<?php
include('./config/db.php');
include('./includes/header.php');
?>


<!-- Page Title -->
<div class="d-flex justify-content-between align-items-center mt-3 mb-3" style="padding-top:0.5rem;">
  <h3 class="fw-bold text-primary mb-0">📏 Measurements</h3>
  <a href="measurement_add.php" class="btn btn-success fw-semibold shadow-sm">
    ➕ Add Measurement
  </a>
</div>

<!-- Filter Section -->
<div class="card shadow-sm border rounded-3 mb-4">
  <!-- Heading -->
  <div class="card-header bg-primary text-white fw-semibold" 
       style="padding:0.25rem 0.75rem; font-size:0.9rem;">
    🔎 Filter Section
  </div>

  <div class="card-body" style="padding:0.5rem;">
    <form id="filterForm" class="row g-2 align-items-end" method="POST">

      <!-- Customer -->
      <div class="col-md-4 col-sm-6">
        <label class="form-label fw-semibold" style="margin-bottom:0.25rem; font-size:0.85rem;">Customer</label>
        <select name="customer_id" id="customer_id" class="form-select form-select-sm w-100" style="padding:0.25rem 0.35rem; font-size:0.85rem;">
          <option value="">All</option>
          <?php
          $cust = $conn->query("SELECT id, name FROM customers ORDER BY name");
          while ($c = $cust->fetch_assoc()) {
              echo '<option value="'.$c['id'].'">'.htmlspecialchars($c['name']).'</option>';
          }
          ?>
        </select>
      </div>

      <!-- Mobile No -->
      <div class="col-md-4 col-sm-6">
        <label class="form-label fw-semibold" style="margin-bottom:0.25rem; font-size:0.85rem;">Mobile No</label>
        <select name="mobile" id="mobile" class="form-select form-select-sm w-100" style="padding:0.25rem 0.35rem; font-size:0.85rem;">
          <option value="">All</option>
          <?php
          $mob = $conn->query("SELECT DISTINCT mobile FROM customers WHERE mobile != '' ORDER BY mobile");
          while ($m = $mob->fetch_assoc()) {
              echo '<option value="'.$m['mobile'].'">'.htmlspecialchars($m['mobile']).'</option>';
          }
          ?>
        </select>
      </div>

      <!-- Garment Type -->
      <div class="col-md-4 col-sm-6">
        <label class="form-label fw-semibold" style="margin-bottom:0.25rem; font-size:0.85rem;">Garment Type</label>
        <select name="garment_type" id="garment_type" class="form-select form-select-sm w-100" style="padding:0.25rem 0.35rem; font-size:0.85rem;">
          <option value="">All</option>
          <?php
          $types = $conn->query("SELECT id, title FROM cloth_subtypes ORDER BY title");
          while ($t = $types->fetch_assoc()) {
              echo '<option value="'.$t['id'].'">'.htmlspecialchars($t['title']).'</option>';
          }
          ?>
        </select>
      </div>

      <!-- From Date -->
      <div class="col-md-3 col-sm-6">
        <label class="form-label fw-semibold" style="margin-bottom:0.25rem; font-size:0.85rem;">From Date</label>
        <input type="date" name="from_date" class="form-control form-control-sm" style="padding:0.25rem; font-size:0.85rem;">
      </div>

      <!-- To Date -->
      <div class="col-md-3 col-sm-6">
        <label class="form-label fw-semibold" style="margin-bottom:0.25rem; font-size:0.85rem;">To Date</label>
        <input type="date" name="to_date" class="form-control form-control-sm" style="padding:0.25rem; font-size:0.85rem;">
      </div>

      <!-- Search Button -->
      <div class="col-md-2 col-sm-6">
        <label class="form-label fw-semibold" style="margin-bottom:0.25rem; font-size:0.85rem;">Action</label>
        <button type="submit" class="btn btn-primary fw-semibold w-100 shadow-sm" style="padding:0.25rem 0.35rem; font-size:0.85rem;">
          🔍 Search
        </button>
      </div>

    </form>
  </div>
</div>





<!-- Table Container -->
<div class="card shadow-sm border-0 rounded-3">
  <div class="card-body">
    <div id="tableContainer" class="table-responsive text-center">
      <p class="text-muted mb-0">Loading measurements...</p>
    </div>
  </div>
</div>

<!-- Measurement Details Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" 
     data-bs-backdrop="static" data-bs-keyboard="false" 
     aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-semibold" id="modalTitle">Measurement Details</h5>
        <button type="button" class="btn-close btn-close-white" 
                data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="measurementDetails">
        <p class="text-muted text-center mb-0">Loading...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- jQuery & Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
jQuery(document).ready(function($) {

  $('#customer_id, #mobile, #garment_type').select2({
    placeholder: "Select option",
    allowClear: true,
    width: '100%'
  });

  // Load measurements via AJAX
  function loadMeasurements() {
    const formData = new FormData(document.getElementById('filterForm'));
    fetch('measurement_fetch.php', { method: 'POST', body: formData })
      .then(res => res.text())
      .then(html => { $('#tableContainer').html(html); })
      .catch(err => {
        console.error(err);
        $('#tableContainer').html("<div class='text-danger p-3'>Error loading data.</div>");
      });
  }

  // Initial load
  loadMeasurements();

  // Handle form submit
  $('#filterForm').on('submit', function(e) {
    e.preventDefault();
    loadMeasurements();
  });

  // Handle View Button Click
  $(document).on('click', '.view-btn', function() {
    const btn = $(this);
    const garment = btn.data('garment') || '';
    const customer = btn.data('customer') || '';
    const mobile = btn.data('mobile') || '';
    const date = btn.data('date') || '';
    const image = btn.data('image');
    let data = {};

    try {
      data = JSON.parse(btn.data('json'));
    } catch {
      console.error("Invalid JSON in data-json attribute");
    }

    let html = '';
    if (image && image !== 'null' && image !== '') {
      html += `<div class="text-center mb-3">
        <img src="uploads/subtypes/${image}" class="img-thumbnail shadow-sm" 
             style="max-width: 180px; border-radius: 8px;">
      </div>`;
    }

    html += '<div class="row">';
    html += `<div class="col-md-6 mb-2"><strong>Customer:</strong> ${customer}</div>`;
    html += `<div class="col-md-6 mb-2"><strong>Mobile:</strong> ${mobile}</div>`;
    html += `<div class="col-md-6 mb-2"><strong>Garment:</strong> ${garment}</div>`;
    html += `<div class="col-md-6 mb-2"><strong>Date:</strong> ${date}</div>`;
    for (const key in data) {
      html += `<div class="col-md-4 mb-2"><strong>${key}:</strong> ${data[key]}</div>`;
    }
    html += '</div>';

    $('#modalTitle').text(`${customer} — ${garment} (${date})`);
    $('#measurementDetails').html(html);
    new bootstrap.Modal(document.getElementById('viewModal')).show();
  });

});
</script>

<style>
.card { transition: 0.2s; }
.card:hover { box-shadow: 0 0 10px rgba(0,0,0,0.08); }
thead th { background: #0d6efd; color: #fff; }
tbody tr:hover { background: #f8f9fa; }
.modal-content { border-radius: 12px; }

#filterForm .form-label {
  font-size: 0.9rem;
  color: #0d6efd;
}
#filterForm select, 
#filterForm input {
  font-size: 0.9rem;
}
</style>

<?php include('./includes/footer.php'); ?>
