<?php
include('./config/db.php');
 include('./includes/header.php');
?>
<h3>Customer Payments</h3>
<table class="table table-bordered"><thead><tr><th>#</th><th>Date</th><th>Customer</th><th>Order No</th><th>Amount</th><th>Mode</th></tr></thead><tbody>
<?php
$sql = "SELECT p.*, o.order_no, c.name AS customer FROM customer_payments p JOIN orders o ON p.order_id=o.id JOIN customers c ON o.customer_id=c.id ORDER BY p.payment_date DESC";
$res = $conn->query($sql); $i=1;
while($r=$res->fetch_assoc()){ echo "<tr><td>{$i}</td><td>{$r['payment_date']}</td><td>".htmlspecialchars($r['customer'])."</td><td>{$r['order_no']}</td><td>{$r['amount']}</td><td>{$r['pay_mode']}</td></tr>"; $i++; }
?>
</tbody></table>
<?php 
    include('./includes/footer.php');
    ?> 