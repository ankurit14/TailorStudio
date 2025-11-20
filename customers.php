<?php
include('../config/db.php');
include('../includes/header.php');
?>

<h3>Customers</h3>
<a href="customer_add.php" class="btn btn-primary mb-3">Add Customer</a>
<table class="table table-bordered">
<thead>
  <tr>
    <th>#</th>
    <th>Name</th>
    <th>Mobile</th>
    <th>Address</th>
    <th>Action</th>
  </tr>
</thead>
<tbody>
<?php
$res = $conn->query("SELECT * FROM customers ORDER BY id DESC");
$i = 1;
while ($row = $res->fetch_assoc()) {
  echo '<tr>
    <td>'.$i.'</td>
    <td>'.htmlspecialchars($row['name']).'</td>
    <td>'.htmlspecialchars($row['mobile']).'</td>
    <td>'.htmlspecialchars($row['address']).'</td>
    <td>
      <a href="customer_edit.php?id='.$row['id'].'" class="btn btn-sm btn-warning">Edit</a>
      <a href="customer_delete.php?id='.$row['id'].'" class="btn btn-sm btn-danger" onclick="return confirm(\'Delete this customer?\')">Delete</a>
    </td>
  </tr>';
  $i++;
}
?>
</tbody>
</table>
<?php include('../includes/footer.php'); ?>
