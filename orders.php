<?php
include('./config/db.php');
include('./includes/header.php');
?>
<!-- Page Title & Add Button -->
<div class="d-flex justify-content-between align-items-center mt-4 mb-3" style="padding-top:0.5rem;">
    <h3 class="fw-bold text-primary mb-0">
        📦 Orders
    </h3>
    <a href="order_add.php" class="btn btn-success fw-semibold shadow-sm">
        ➕ Add New Order
    </a>
</div>

<!-- Orders Table Card -->
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body">
        <div class="table-responsive">
           <table class="table table-bordered table-hover align-middle" style="font-size:0.875rem;">
  <thead class="table-primary text-white">
                    <tr>
                        <th style="padding:0.35rem 0.5rem; vertical-align:middle; width: 60px; width: 60px;">#</th>
                        <th style="padding:0.35rem 0.5rem; vertical-align:middle; width: 60px; width: 130px;">Order No</th>
                        <th style="padding:0.35rem 0.5rem; vertical-align:middle; width: 60px; width: 200px;">Customer</th>
                        <th style="padding:0.35rem 0.5rem; vertical-align:middle; width: 60px; width: 200px;">Karigar</th>
                        <th style="padding:0.35rem 0.5rem; vertical-align:middle; width: 60px; width: 150px;">Date</th>
                        <th style="padding:0.35rem 0.5rem; vertical-align:middle; width: 60px; width: 130px;">Total (₹)</th>
                        <th style="padding:0.35rem 0.5rem; vertical-align:middle; width: 60px; width: 130px;">Status</th>
                        <th style="padding:0.35rem 0.5rem; vertical-align:middle; width: 60px; width: 120px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
$q = "SELECT o.*, c.name AS customer, k.name AS karigar 
      FROM orders o 
      LEFT JOIN customers c ON o.customer_id=c.id 
      LEFT JOIN karigars k ON o.karigar_id=k.id 
      ORDER BY o.id DESC";
$res = $conn->query($q);
$i = 1;

if ($res->num_rows > 0) {
    while ($r = $res->fetch_assoc()) {

        // status badge
        $statusClass = 'secondary';
        if ($r['status'] == 'Pending') $statusClass = 'warning';
        elseif ($r['status'] == 'Completed') $statusClass = 'success';
        elseif ($r['status'] == 'Cancelled') $statusClass = 'danger';

        echo "
        <tr>
            <td style='padding:0.35rem 0.5rem; vertical-align:middle;'>{$i}</td>
            <td style='padding:0.35rem 0.5rem; vertical-align:middle;'>" . htmlspecialchars($r['order_no']) . "</td>
            <td style='padding:0.35rem 0.5rem; vertical-align:middle;' class='text-start'>" . htmlspecialchars($r['customer']) . "</td>
            <td style='padding:0.35rem 0.5rem; vertical-align:middle;' class='text-start'>" . htmlspecialchars($r['karigar']) . "</td>
            <td style='padding:0.35rem 0.5rem; vertical-align:middle;'>" . htmlspecialchars($r['order_date']) . "</td>
            <td style='padding:0.35rem 0.5rem; vertical-align:middle;'><strong>" . number_format($r['total_amount'], 2) . "</strong></td>
            <td style='padding:0.35rem 0.5rem; vertical-align:middle;'>
                <span class='badge bg-{$statusClass} px-3 py-2'>
                    " . htmlspecialchars($r['status']) . "
                </span>
            </td>
            <td style='padding:0.35rem 0.5rem; vertical-align:middle;'>
                <a href='order_view.php?id=".$r['id']."' 
                   class='btn btn-sm btn-outline-primary shadow-sm'
                   title='View Order'>
                   👁️ View
                </a>
                <a href='order_print.php?id=".$r['id']."' 
                   class='btn btn-sm btn-outline-success shadow-sm ms-1'
                   target='_blank' 
                   title='Print Order'>
                   🖨️ Print
                </a>
            </td>
        </tr>";
        $i++;
    }
} else {
    echo "<tr><td colspan='8' class='text-center text-muted py-3'>No orders found.</td></tr>";
}
?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Inline Styling for Consistency -->
<style>
    table.table {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        overflow: hidden;
        font-size: 15px;
    }

    thead th {
        font-weight: 600;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }

    tbody tr:hover {
        background-color: #f8f9fa !important;
        transition: background-color 0.2s ease-in-out;
    }

    .btn-outline-primary {
        color: #0d6efd;
        border-color: #0d6efd;
        transition: all 0.2s ease-in-out;
    }
    .btn-outline-primary:hover {
        background-color: #0d6efd;
        color: #fff;
    }

    .card-body {
        padding: 1.5rem 1.5rem 1rem;
    }

    .badge {
        font-size: 0.85rem;
        border-radius: 8px;
    }
</style>

<?php include('./includes/footer.php'); ?>
