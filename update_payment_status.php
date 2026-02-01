<?php
include('./config/db.php');

$id = $_GET['id'];
$status = $_GET['status'];

$conn->query("UPDATE payments SET status='$status' WHERE id=$id");

header('Location: payments.php');
exit;
?>
