<?php include('../includes/header.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $mobile = trim($_POST['mobile']);
    $address = trim($_POST['address']);
    if ($name != '') {
        $stmt = $conn->prepare("INSERT INTO customers (name,mobile,address) VALUES (?,?,?)");
        $stmt->bind_param("sss", $name, $mobile, $address);
        if ($stmt->execute()) {
            echo "<script>alert('Customer added'); window.location='customers.php';</script>";
        } else {
            echo "<div class='alert alert-danger'>".$conn->error."</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='alert alert-warning'>Name required</div>";
    }
}
?>
<div class="card"><div class="card-header bg-primary text-white">Add Customer</div>
<div class="card-body">
<form method="post">
<div class="mb-3"><label>Name</label><input name="name" class="form-control" required></div>
<div class="mb-3"><label>Mobile</label><input name="mobile" class="form-control"></div>
<div class="mb-3"><label>Address</label><textarea name="address" class="form-control"></textarea></div>
<button class="btn btn-success">Save</button> <a href="customers.php" class="btn btn-secondary">Back</a>
</form>
</div></div>
<?php include('../includes/footer.php'); ?>