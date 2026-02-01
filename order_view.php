<?php
include('./config/db.php');
include('./includes/header.php');

// Validate ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>window.location='orders.php';</script>"; 
    exit; 
}
$id = intval($_GET['id']);

// Fetch order details
$order = $conn->query("
    SELECT o.*, c.name AS customer, k.name AS karigar 
    FROM orders o 
    LEFT JOIN customers c ON o.customer_id=c.id 
    LEFT JOIN karigars k ON o.karigar_id=k.id 
    WHERE o.id=$id
")->fetch_assoc();

// Fetch order items with garment names and sub-item titles
$items = $conn->query("
    SELECT 
        oi.*, 
        ct.title AS garment_name, 
        cs.title AS sub_item_title
    FROM order_items oi
    LEFT JOIN cloth_types ct ON oi.garment_type = ct.id
    LEFT JOIN cloth_subtypes cs ON oi.sub_item_id = cs.id
    WHERE oi.order_id = $id
");
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Order Details</h2>
        <a href="orders.php" class="btn btn-secondary">← Back to Orders</a>
    </div>

    <!-- Order Summary Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <strong>Customer:</strong> <?php echo htmlspecialchars($order['customer']); ?>
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Karigar:</strong> <?php echo htmlspecialchars($order['karigar']); ?>
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?>
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Delivery Date:</strong> <?php echo htmlspecialchars($order['delivery_date'] ?? 'N/A'); ?>
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Trial Date:</strong> <?php echo htmlspecialchars($order['trial_date'] ?? 'N/A'); ?>
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Status:</strong> 
                    <?php 
                    $statusClass = 'secondary';
                    if ($order['status'] == 'Pending') $statusClass = 'warning';
                    elseif ($order['status'] == 'Completed') $statusClass = 'success';
                    elseif ($order['status'] == 'Cancelled') $statusClass = 'danger';
                    ?>
                    <span class="badge bg-<?php echo $statusClass; ?>"><?php echo htmlspecialchars($order['status']); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items Table -->
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-primary text-white">
                    <tr>
                        <th>#</th>
                        <th>Garment Type</th>
                        <th>Sub Item</th>
                        <th>Quantity</th>
                        <th>Rate (₹)</th>
                        <th>Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    $grand_total = 0;
                    if($items->num_rows > 0) {
                        while($item = $items->fetch_assoc()) {
                            $amount = $item['quantity'] * $item['rate'];
                            $grand_total += $amount;
                            echo "<tr>
                                <td>{$i}</td>
                                <td>".htmlspecialchars($item['garment_name'] ?? 'N/A')."</td>
                                <td>".htmlspecialchars($item['sub_item_title'] ?? 'N/A')."</td>
                                <td>{$item['quantity']}</td>
                                <td>".number_format($item['rate'],2)."</td>
                                <td>".number_format($amount,2)."</td>
                            </tr>";
                            $i++;
                        }
                        echo "<tr>
                            <td colspan='5' class='text-end fw-bold'>Total</td>
                            <td class='fw-bold'>".number_format($grand_total,2)."</td>
                        </tr>";
                    } else {
                        echo "<tr><td colspan='6' class='text-center text-muted'>No items added to this order.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 10px;
    }
    .card-body p, .card-body strong {
        font-size: 0.95rem;
    }
    table.table th, table.table td {
        text-align: center;
        vertical-align: middle;
    }
    table.table th {
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.45em 0.75em;
        border-radius: 8px;
    }
</style>

<?php include('./includes/footer.php'); ?>