<?php
include('./config/db.php');

$order_id = intval($_GET['id']);

// Fetch order info including the new trial_date column
$order = $conn->query("
    SELECT 
        o.*, 
        c.name AS customer, 
        c.mobile AS customer_phone, 
        c.address AS customer_address, 
        k.name AS karigar
    FROM orders o
    LEFT JOIN customers c ON o.customer_id = c.id
    LEFT JOIN karigars k ON o.karigar_id = k.id
    WHERE o.id = $order_id
")->fetch_assoc();

// Fetch order items
$items = $conn->query("
    SELECT oi.*, cs.title AS sub_item_title
    FROM order_items oi
    LEFT JOIN cloth_subtypes cs ON oi.sub_item_id = cs.id
    WHERE oi.order_id = $order_id
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Order Invoice - <?= htmlspecialchars($order['order_no']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #fafafa;
        }
        .container {
            max-width: 850px;
            margin: auto;
            background: #fff;
            border: 1px solid #ddd;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #224422;
            margin-bottom: 10px;
            font-weight: 700;
        }
        .shop-name {
            text-align: center;
            font-weight: 600;
            font-size: 18px;
            color: #2e2e2e;
            margin-bottom: 30px;
        }
        .details-wrapper {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .details {
            flex: 1 1 45%;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            background: #fefefe;
            box-shadow: 0 0 8px rgba(0,0,0,0.05);
        }
        .details h3 {
            margin-top: 0;
            color: #224422;
            border-bottom: 2px solid #224422;
            padding-bottom: 6px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        table, th, td {
            border: 1px solid #aaa;
        }
        th, td {
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background-color: #224422;
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
        }
        .total-row td {
            font-weight: bold;
            text-align: right;
        }
        .btn-print {
            float: right;
            margin-bottom: 15px;
            padding: 10px 18px;
            background-color: #224422;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .btn-print:hover {
            background-color: #196619;
        }
        @media print {
            .btn-print { display: none; }
            body { margin: 0; }
            .container {
                box-shadow: none;
                border: none;
                max-width: 100%;
                padding: 0;
                border-radius: 0;
            }
        }
        @media (max-width: 700px) {
            .details-wrapper { flex-direction: column; }
            .details { flex: 1 1 100%; margin-bottom: 20px; }
        }
    </style>
</head>
<body>
<div class="container">
    <button class="btn-print" onclick="window.print()">🖨️ Print / Save PDF</button>
    <h2>🧵 Order Receipt</h2>
    <div class="shop-name"><strong>Shop Name:</strong> Unique Tailor Shop</div>

    <div class="details-wrapper">
        <!-- Customer Details -->
        <div class="details">
            <h3>Customer Details</h3>
            <table>
                <tr><th>Name</th><td><?= htmlspecialchars($order['customer']) ?></td></tr>
                <tr><th>Mobile</th><td><?= htmlspecialchars($order['customer_phone']) ?></td></tr>
                <tr><th>Address</th><td><?= htmlspecialchars($order['customer_address']) ?></td></tr>
            </table>
        </div>

        <!-- Order Details -->
        <div class="details">
            <h3>Order Details</h3>
            <table>
                <tr><th>Order No</th><td><?= htmlspecialchars($order['order_no']) ?></td></tr>
                <tr><th>Karigar</th><td><?= htmlspecialchars($order['karigar']) ?></td></tr>
                <tr><th>Order Date</th><td><?= htmlspecialchars($order['order_date']) ?></td></tr>
                <tr><th>Approx. Delivery Date</th><td><?= htmlspecialchars($order['delivery_date']) ?></td></tr>
                <tr><th>Approx. Trial Date</th><td><?= htmlspecialchars($order['trial_date']) ?></td></tr>
                <tr><th>Status</th><td><?= htmlspecialchars($order['status']) ?></td></tr>
            </table>
        </div>
    </div>

    <h3>Garment Details</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Rate (₹)</th>
                <th>Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            $total = 0;
            while ($item = $items->fetch_assoc()):
                $amount = $item['quantity'] * $item['rate'];
                $total += $amount;
            ?>
            <tr>
                <td><?= $i ?></td>
                <td><?= htmlspecialchars($item['sub_item_title']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= number_format($item['rate'], 2) ?></td>
                <td><?= number_format($amount, 2) ?></td>
            </tr>
            <?php $i++; endwhile; ?>
            <tr class="total-row">
                <td colspan="4">Total Amount:</td>
                <td>₹<?= number_format($order['total_amount'], 2) ?></td>
            </tr>
            <tr class="total-row">
                <td colspan="4">Advance Paid:</td>
                <td>₹<?= number_format($order['advance_paid'], 2) ?></td>
            </tr>
        </tbody>
    </table>
</div>
</body>
</html>
