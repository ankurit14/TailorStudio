<?php
include('./config/db.php');
 include('./includes/header.php');

$karigars = $conn->query("SELECT id,name FROM karigars ORDER BY name");
if($_SERVER['REQUEST_METHOD']=='POST'){ $karigar_id = intval($_POST['karigar_id']); $date=$_POST['payment_date']; $paid = floatval($_POST['paid_amount']); $rem = $_POST['remarks'];
 $res = $conn->query("SELECT SUM(oi.karigar_charge) AS total FROM orders o JOIN order_items oi ON o.id=oi.order_id WHERE o.karigar_id=$karigar_id AND oi.delivery_status='Completed'");
 $total = $res->fetch_assoc()['total'] ?? 0;
 $stmt = $conn->prepare("INSERT INTO karigar_payments (karigar_id,payment_date,total_work,paid_amount,remarks) VALUES (?,?,?,?,?)");
 $stmt->bind_param("isdss", $karigar_id, $date, $total, $paid, $rem); $stmt->execute(); echo "<script>alert('Saved'); window.location='karigar_payments.php';</script>"; }
?>
<div class="card"><div class="card-header bg-warning text-white">Add Karigar Payment</div><div class="card-body"><form method="post"><div class="row"><div class="col-md-4"><label>Karigar</label><select name="karigar_id" class="form-control" required><option value=''>--Select--</option><?php while($k=$karigars->fetch_assoc()){ echo "<option value='{$k['id']}'>{$k['name']}</option>"; } ?></select></div><div class="col-md-3"><label>Date</label><input type="date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>"></div><div class="col-md-3"><label>Paid Amount</label><input type="number" name="paid_amount" step="0.01" class="form-control" required></div></div><div class="mt-3"><label>Remarks</label><textarea name="remarks" class="form-control"></textarea></div><button class="btn btn-success mt-3">Save</button></form></div></div>
