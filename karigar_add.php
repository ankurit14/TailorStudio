<?php
include('./config/db.php');
include('./includes/header.php');

$nameErr = $mobileErr = $duplicateErr = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name  = trim($_POST['name']);
    $mobile = trim($_POST['mobile']);
    $spec  = trim($_POST['specialization']);

    // Name validation
    if ($name == "") {
        $nameErr = "Name is required.";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $name)) {
        $nameErr = "Name can only contain letters and spaces.";
    }

    // Mobile validation
    if ($mobile != "" && !preg_match("/^[0-9]{10}$/", $mobile)) {
        $mobileErr = "Mobile must be exactly 10 digits.";
    }

    // Check duplicate Name + Mobile
    if ($nameErr == "" && $mobileErr == "") {
        $dupStmt = $conn->prepare("SELECT id FROM karigars WHERE name=? AND mobile=?");
        $dupStmt->bind_param("ss", $name, $mobile);
        $dupStmt->execute();
        $dupRes = $dupStmt->get_result();
        if ($dupRes->num_rows > 0) {
            $duplicateErr = "A Karigar with the same Name and Mobile already exists.";
        }
        $dupStmt->close();
    }

    // Insert if no errors
    if ($nameErr == "" && $mobileErr == "" && $duplicateErr == "") {
        $stmt = $conn->prepare("INSERT INTO karigars (name, mobile, specialization) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $mobile, $spec);
        if ($stmt->execute()) {
            echo "<script>
                    alert('Karigar added successfully');
                    window.location='karigars.php';
                  </script>";
            exit;
        } else {
            echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
        $stmt->close();
    }
}
?>

<!-- Page Title -->
<div class="d-flex justify-content-between align-items-center mt-4 mb-3">
    <h3 class="fw-bold text-primary mb-0">
        🧵 Add New Karigar
    </h3>
    <a href="karigars.php" class="btn btn-outline-secondary">
        ← Back to List
    </a>
</div>

<!-- Form Card -->
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body">

        <!-- Error Messages -->
        <?php if($duplicateErr): ?>
            <div class="alert alert-danger"><?php echo $duplicateErr; ?></div>
        <?php endif; ?>
        <?php if($nameErr): ?>
            <div class="alert alert-danger"><?php echo $nameErr; ?></div>
        <?php endif; ?>
        <?php if($mobileErr): ?>
            <div class="alert alert-danger"><?php echo $mobileErr; ?></div>
        <?php endif; ?>

        <form method="post" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">
                        Name <span class="text-danger">*</span>
                    </label>
                    <input 
                        name="name" 
                        type="text" 
                        class="form-control" 
                        placeholder="Enter karigar name" 
                        required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Mobile</label>
                    <input 
                        name="mobile" 
                        type="number" 
                        class="form-control" 
                        placeholder="Enter 10-digit mobile number">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Specialization</label>
                <input 
                    name="specialization" 
                    type="text" 
                    class="form-control" 
                    placeholder="Enter specialization (e.g., Coat, Kurta, etc.)">
            </div>

            <div class="d-flex justify-content-end">
                <button class="btn btn-success px-4 me-2" type="submit">
                    💾 Save Karigar
                </button>
                <a href="karigars.php" class="btn btn-secondary px-4">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Styling -->
<style>
    .card {
        transition: box-shadow 0.2s ease-in-out;
    }

    .card:hover {
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.08);
    }

    input.form-control {
        border-radius: 8px;
    }

    .btn {
        font-weight: 500;
    }
</style>

<?php include('./includes/footer.php'); ?>
