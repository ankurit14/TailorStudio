<?php
include('./config/db.php');
include('./includes/header.php');

$nameErr = $mobileErr = $duplicateErr = "";
$name = $mobile = $address = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name    = trim($_POST['name']);
    $mobile  = trim($_POST['mobile']);
    $address = trim($_POST['address']);

    $valid = true;

    // Name validation
    if ($name == "") {
        $nameErr = "Name is required";
        $valid = false;
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $name)) {
        $nameErr = "Name can contain only letters and spaces";
        $valid = false;
    }

    // Mobile validation
    if ($mobile != "" && !preg_match("/^[0-9]{10}$/", $mobile)) {
        $mobileErr = "Mobile must be exactly 10 digits";
        $valid = false;
    }

    // Duplicate check: name + mobile combination
    if ($valid) {
        $stmt = $conn->prepare("SELECT id FROM customers WHERE name=? AND mobile=? LIMIT 1");
        $stmt->bind_param("ss", $name, $mobile);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 0) {
            $duplicateErr = "A customer with the same Name and Mobile already exists.";
            $valid = false;
        }
        $stmt->close();
    }

    // Insert if valid
    if ($valid) {
        $stmt = $conn->prepare("INSERT INTO customers (name, mobile, address) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $mobile, $address);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Customer added successfully');
                    window.location='customers.php';
                  </script>";
        } else {
            echo "<div class='alert alert-danger'>" . htmlspecialchars($conn->error) . "</div>";
        }

        $stmt->close();
    }
}
?>

<!-- Page Title Section -->
<div class="d-flex justify-content-between align-items-center mt-4 mb-3">
    <h3 class="fw-bold text-primary mb-0">
        🧾 Add New Customer
    </h3>
    <a href="customers.php" class="btn btn-outline-secondary">
        ← Back to List
    </a>
</div>

<!-- Form Card Section -->
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body">
        <?php if($duplicateErr): ?>
            <div class="alert alert-danger"><?php echo $duplicateErr; ?></div>
        <?php endif; ?>

        <form method="post" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">
                        Name <span class="text-danger">*</span>
                    </label>
                    <input name="name" type="text" class="form-control <?php echo ($nameErr) ? 'is-invalid' : ''; ?>" 
                           placeholder="Enter customer name" value="<?php echo htmlspecialchars($name); ?>" required>
                    <div class="invalid-feedback"><?php echo $nameErr; ?></div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Mobile</label>
                    <input name="mobile" type="number" class="form-control <?php echo ($mobileErr) ? 'is-invalid' : ''; ?>" 
                           placeholder="Enter 10-digit mobile number" value="<?php echo htmlspecialchars($mobile); ?>">
                    <div class="invalid-feedback"><?php echo $mobileErr; ?></div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Address</label>
                <textarea name="address" class="form-control" placeholder="Enter address" rows="3"><?php echo htmlspecialchars($address); ?></textarea>
            </div>

            <div class="d-flex justify-content-end">
                <button class="btn btn-success px-4 me-2" type="submit">
                    💾 Save Customer
                </button>
                <a href="customers.php" class="btn btn-secondary px-4">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Client-side validation -->
<script>
(() => {
  'use strict'
  const forms = document.querySelectorAll('.needs-validation')
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      const nameField = form.querySelector('[name="name"]');
      const mobileField = form.querySelector('[name="mobile"]');
      const namePattern = /^[a-zA-Z ]+$/;
      const mobilePattern = /^[0-9]{10}$/;
      let valid = true;

      if (!namePattern.test(nameField.value)) {
        nameField.classList.add('is-invalid');
        nameField.nextElementSibling.textContent = "Name can contain only letters and spaces";
        valid = false;
      } else {
        nameField.classList.remove('is-invalid');
      }

      if (mobileField.value !== "" && !mobilePattern.test(mobileField.value)) {
        mobileField.classList.add('is-invalid');
        mobileField.nextElementSibling.textContent = "Mobile must be exactly 10 digits";
        valid = false;
      } else {
        mobileField.classList.remove('is-invalid');
      }

      if (!valid) {
        event.preventDefault();
        event.stopPropagation();
      }
    }, false)
  })
})();
</script>

<?php include('./includes/footer.php'); ?>
