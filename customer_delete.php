<?php include('../includes/header.php');
if (!isset($_GET['id'])||!is_numeric($_GET['id'])) { echo "<script>window.location='customers.php';</script>"; exit; }
$id=intval($_GET['id']);
$stmt=$conn->prepare("DELETE FROM customers WHERE id=?"); $stmt->bind_param("i", $id);
if($stmt->execute()){ echo "<script>alert('Deleted'); window.location='customers.php';</script>"; } else { echo "<div class='alert alert-danger'>".$conn->error."</div>"; }
?>