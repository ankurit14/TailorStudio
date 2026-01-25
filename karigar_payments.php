<?php
include('./config/db.php');
 include('./includes/header.php');
?>
<h3>Karigar Payments</h3>
<a href="karigar_payment_add.php" class="btn btn-primary mb-3">Add Payment</a>
<table class="table table-bordered"><thead><tr><th>#</th><th>Date</th><th>Karigar</th><th>Total Work</th><th>Paid</th><th>Balance</th></tr></thead><tbody>
<?php $sql="SELECT kp.*, k.name AS karigar FROM karigar_payments kp JOIN karigars k ON kp.karigar_id=k.id ORDER BY kp.payment_date DESC"; $res=$conn->query($sql); $i=1; while($r=$res->fetch_assoc()){ echo "<tr><td>{$i}</td><td>{$r['payment_date']}</td><td>".htmlspecialchars($r['karigar'])."</td><td>{$r['total_work']}</td><td>{$r['paid_amount']}</td><td>{$r['balance']}</td></tr>"; $i++; } ?>
</tbody></table>
<?php 
    include('./includes/footer.php');
    ?> 