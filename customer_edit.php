<?php include('../includes/header.php');
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { echo "<script>window.location='customers.php';</script>"; exit; }
$id=intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM customers WHERE id=?");
$stmt->bind_param("i", $id); $stmt->execute(); $res=$stmt->get_result(); $customer=$res->fetch_assoc();
if (!$customer) { echo "<script>window.location='customers.php';</script>"; exit; }
if ($_SERVER['REQUEST_METHOD']=='POST') {
  $name=trim($_POST['name']); $mobile=trim($_POST['mobile']); $address=trim($_POST['address']);
  $up = $conn->prepare("UPDATE customers SET name=?, mobile=?, address=? WHERE id=?");
  $up->bind_param("sssi", $name, $mobile, $address, $id);
  if ($up->execute()) { echo "<script>alert('Updated'); window.location='customers.php';</script>"; }
}
?>
<div class="card"><div class="card-header bg-warning text-white">Edit Customer</div>
<div class="card-body">
<form method="post">
<div class="mb-3"><label>Name</label><input name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" class="form-control" required></div>
<div class="mb-3"><label>Mobile</label><input name="mobile" value="<?php echo htmlspecialchars($customer['mobile']); ?>" class="form-control"></div>
<div class="mb-3"><label>Address</label><textarea name="address" class="form-control"><?php echo htmlspecialchars($customer['address']); ?></textarea></div>
<button class="btn btn-success">Update</button> <a href="customers.php" class="btn btn-secondary">Back</a>
</form>
</div></div>
<?php include('../includes/footer.php'); ?>