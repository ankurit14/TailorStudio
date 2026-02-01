<?php 
include('./includes/header.php');
include('./config/db.php');

$payments = $conn->query("SELECT p.*, c.name AS customer_name, o.order_no 
                          FROM payments p
                          LEFT JOIN customers c ON p.customer_id = c.id
                          LEFT JOIN orders o ON p.order_id = o.id
                          ORDER BY p.id DESC");
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3" style="padding-top:0.5rem;">
        <h4><i class="ti-wallet"></i> Payment Records</h4>
        <a href="add_payment.php" class="btn btn-success"><i class="ti-plus"></i> Add Payment</a>
    </div>

    <table class="table table-bordered table-hover align-middle" style="font-size:0.875rem;">
  <thead class="table-primary text-white">
            <tr>
                <th style="padding:0.35rem 0.5rem; vertical-align:middle;">ID</th>
                <th style="padding:0.35rem 0.5rem; vertical-align:middle;">Date</th>
                <th style="padding:0.35rem 0.5rem; vertical-align:middle;">Customer</th>
                <th style="padding:0.35rem 0.5rem; vertical-align:middle;">Order</th>
                <th style="padding:0.35rem 0.5rem; vertical-align:middle;">Amount</th>
                <th style="padding:0.35rem 0.5rem; vertical-align:middle;">Method</th>
                <th style="padding:0.35rem 0.5rem; vertical-align:middle;">Status</th>
                <th style="padding:0.35rem 0.5rem; vertical-align:middle;">Notes</th>
                <th style="padding:0.35rem 0.5rem; vertical-align:middle;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($p = $payments->fetch_assoc()) { ?>
                <tr>
                    <td style='padding:0.35rem 0.5rem; vertical-align:middle;'><?= $p['id'] ?></td>
                    <td style='padding:0.35rem 0.5rem; vertical-align:middle;'><?= $p['payment_date'] ?></td>
                    <td style='padding:0.35rem 0.5rem; vertical-align:middle;'><?= $p['customer_name'] ?: '-' ?></td>
                    <td style='padding:0.35rem 0.5rem; vertical-align:middle;'><?= $p['order_no'] ?: '-' ?></td>
                    <td style='padding:0.35rem 0.5rem; vertical-align:middle;'><?= number_format($p['amount'], 2) ?></td>
                    <td style='padding:0.35rem 0.5rem; vertical-align:middle;'><?= $p['method'] ?></td>
                    <td style='padding:0.35rem 0.5rem; vertical-align:middle;'>
                        <span class="badge 
                            <?= $p['status'] == 'Completed' ? 'bg-success' : ($p['status'] == 'Pending' ? 'bg-warning' : 'bg-danger') ?>">
                            <?= $p['status'] ?>
                        </span>
                    </td>
                    <td style='padding:0.35rem 0.5rem; vertical-align:middle;'><?= $p['notes'] ?></td>
                    <td style='padding:0.35rem 0.5rem; vertical-align:middle;'>
                        <a href="update_payment_status.php?id=<?= $p['id'] ?>&status=Completed" class="btn btn-sm btn-success">Mark Completed</a>
                        <a href="update_payment_status.php?id=<?= $p['id'] ?>&status=Cancelled" class="btn btn-sm btn-danger">Cancel</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include('./includes/footer.php'); ?>
