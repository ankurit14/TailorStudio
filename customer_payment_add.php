<?php
include('./config/db.php');
 include('./includes/header.php');

$order_id = isset($_GET['order_id'])?intval($_GET['order_id']):0;
$order = $conn->query("SELECT o.*, c.name AS customer FROM orders o JOIN customers c ON o.customer_id=c.id WHERE o.id=$order_id")->fetch_assoc();
if($_SERVER['REQUEST_METHOD']=='POST'){ $date=$_POST['payment_date']; $amt=floatval($_POST['amount']); $mode=$_POST['pay_mode']; $rem=$_POST['remarks'];
 $stmt=$conn->prepare("INSERT INTO customer_payments (order_id,payment_date,amount,pay_mode,remarks) VALUES (?,?,?,?,?)"); $stmt->bind_param("isdss", $order_id, $date, $amt, $mode, $rem); $stmt->execute();
 $conn->query("UPDATE orders SET advance_paid = advance_paid + $amt WHERE id=$order_id");
 echo "<script>alert('Saved'); window.location='customer_payments.php';</script>"; }
?>
<div class="card"><div class="card-header bg-success text-white">Add Payment for <?php echo htmlspecialchars($order['customer']); ?></div>
<div class="card-body">
<form method="post"><div class="row"><div class="col-md-3"><label>Date</label><input type="date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>"></div><div class="col-md-3"><label>Amount</label><input type="number" name="amount" step="0.01" class="form-control" required></div><div class="col-md-3"><label>Mode</label><select name="pay_mode" class="form-control"><option>Cash</option><option>UPI</option><option>Card</option><option>Bank</option></select></div></div><div class="mt-3"><label>Remarks</label><textarea name="remarks" class="form-control"></textarea></div><button class="btn btn-success mt-3">Save</button></form></div></div>
<?php 
    include('./includes/footer.php');
    ?> 