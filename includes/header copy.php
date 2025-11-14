<?php include(__DIR__ . '/../config/db.php'); ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Tailoring Shop Management</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
  <a class="navbar-brand" href="../reports/dashboard.php">Tailor Shop</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div id="navMenu" class="collapse navbar-collapse">
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a href="../customers/customers.php" class="nav-link">Customers</a></li>
      <li class="nav-item"><a href="../karigars/karigars.php" class="nav-link">Karigars</a></li>
      <li class="nav-item"><a href="../orders/orders.php" class="nav-link">Orders</a></li>
      <li class="nav-item"><a href="../payments/customer_payments.php" class="nav-link">Payments</a></li>
      <li class="nav-item"><a href="../reports/dashboard.php" class="nav-link">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="../measurements/measurements.php">Measurements</a></li>
</ul>
  </div>
  </div>
</nav>
<div class="container mt-4">