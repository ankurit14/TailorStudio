<?php
include('./config/db.php');
include('./includes/header.php');

// Fetch customers and karigars
$customers = $conn->query("SELECT id,name FROM customers ORDER BY name");
$karigars  = $conn->query("SELECT id,name FROM karigars ORDER BY name");

// Fetch cloth types
$cloth_types = $conn->query("SELECT id, title, gender, note FROM cloth_types ORDER BY title");

// Fetch cloth subtypes
$subtypes = [];
$subtype_result = $conn->query("SELECT id, cloth_type_id, title FROM cloth_subtypes ORDER BY title");
while ($row = $subtype_result->fetch_assoc()) {
    $subtypes[$row['cloth_type_id']][] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // AUTO INCREMENT ORDER NUMBER (Sequential)
    $lastOrder = $conn->query("SELECT order_no FROM orders ORDER BY id DESC LIMIT 1")->fetch_assoc();
    if ($lastOrder) {
        $num = intval(substr($lastOrder['order_no'], 3)) + 1;
        $order_no = "ORD" . str_pad($num, 5, "0", STR_PAD_LEFT);
    } else {
        $order_no = "ORD00001";
    }

    $customer_id = intval($_POST['customer_id']);
    $karigar_id  = !empty($_POST['karigar_id']) ? intval($_POST['karigar_id']) : NULL;
    $order_date  = $_POST['order_date'];
    $delivery_date = $_POST['delivery_date'];
    $trial_date = $_POST['trial_date'];
    $total_amount = floatval($_POST['total_amount']);
    $advance      = floatval($_POST['advance_paid']);
    $status = 'Pending';

    // Insert into orders table
    $stmt = $conn->prepare("
        INSERT INTO orders (
            order_no, customer_id, karigar_id, order_date, delivery_date, trial_date, total_amount, advance_paid, status
        ) VALUES (?,?,?,?,?,?,?,?,?)
    ");
    $stmt->bind_param("siisssdds", $order_no, $customer_id, $karigar_id, $order_date, $delivery_date, $trial_date, $total_amount, $advance, $status);
    $stmt->execute();

    $order_id = $conn->insert_id;

    // Insert Items
    if (isset($_POST['item_name'])) {
        foreach ($_POST['item_name'] as $i => $item_id) {

            $sub_item_id = !empty($_POST['sub_item_name'][$i]) ? intval($_POST['sub_item_name'][$i]) : NULL;
            $qty  = intval($_POST['quantity'][$i]);
            $rate = floatval($_POST['rate'][$i]);

            // Insert into order_items
            $stmt2 = $conn->prepare("
                INSERT INTO order_items (order_id, garment_type, sub_item_id, quantity, rate)
                VALUES (?,?,?,?,?)
            ");
            $stmt2->bind_param("iiiid", $order_id, $item_id, $sub_item_id, $qty, $rate);
            $stmt2->execute();
        }
    }

    echo "<script>alert('Order created successfully'); window.location='orders.php';</script>";
}
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mt-4 mb-3">
    <h3 class="fw-bold text-primary mb-0">🛒 Add New Order</h3>
    <a href="orders.php" class="btn btn-outline-secondary">← Back to Orders</a>
</div>

<!-- Order Form Card -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body">
        <form method="post">

            <!-- Customer Info -->
            <div class="row mb-3">
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Customer</label>
                    <select name="customer_id" class="form-control" required>
                        <option value="">-- Select --</option>
                        <?php while ($c = $customers->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Karigar</label>
                    <select name="karigar_id" class="form-control">
                        <option value="">-- Select --</option>
                        <?php while ($k = $karigars->fetch_assoc()): ?>
                            <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label fw-semibold">Order Date</label>
                    <input type="date" name="order_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
<div class="col-md-2 mb-3">
                    <label class="form-label fw-semibold">Trial Date</label>
                    <input type="date" name="trial_date" class="form-control">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-semibold">Delivery Date</label>
                    <input type="date" name="delivery_date" class="form-control">
                </div>

                
            </div>

            <hr>

            <!-- Items Section -->
            <h6 class="fw-semibold mb-3">Items</h6>
            <div class="table-responsive">
                <table class="table table-bordered" id="itemsTable" style="font-size:0.875rem;">
                    <thead class="table-primary text-white">
                        <tr>
                            <th>Item</th>
                            <th>Sub Item</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                <select name="item_name[]" class="form-select item-select" onchange="loadSubItems(this)" required>
                                    <option value="">-- Select Item --</option>
                                    <?php $cloth_types->data_seek(0); while ($c = $cloth_types->fetch_assoc()): ?>
                                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['title']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </td>

                            <td>
                                <select name="sub_item_name[]" class="form-select sub-item-select">
                                    <option value="">-- Select Sub Item --</option>
                                </select>
                            </td>

                            <td><input type="number" name="quantity[]" class="form-control qty" value="1" min="1" onchange="calculateAmounts()"></td>
                            <td><input type="number" step="0.01" name="rate[]" class="form-control rate" onchange="calculateAmounts()"></td>
                            <td><input type="number" step="0.01" class="form-control amount" readonly></td>

                            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">×</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button type="button" class="btn btn-secondary mb-3" onclick="addRow()">+ Add Item</button>

            <!-- Total -->
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Total Amount</label>
                    <input type="number" step="0.01" name="total_amount" id="totalAmount" class="form-control" readonly>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Advance Paid</label>
                    <input type="number" step="0.01" name="advance_paid" class="form-control">
                </div>
            </div>

            <button class="btn btn-success mt-3 px-4">💾 Save Order</button>
        </form>
    </div>
</div>

<script>
// Subtypes from PHP
const subtypes = <?= json_encode($subtypes); ?>;

// Load sub-items dynamically
function loadSubItems(select) {
    const row = select.closest('tr');
    const subSelect = row.querySelector('.sub-item-select');
    subSelect.innerHTML = '<option value="">-- Select Sub Item --</option>';

    const clothTypeId = select.value;

    if (clothTypeId && subtypes[clothTypeId]) {
        subtypes[clothTypeId].forEach(sub => {
            let op = document.createElement('option');
            op.value = sub.id;
            op.textContent = sub.title;
            subSelect.appendChild(op);
        });
    }
}

// Add row
function addRow() {
    let row = document.querySelector('#itemsTable tbody tr').cloneNode(true);

    row.querySelectorAll('input').forEach(i => i.value = '');
    row.querySelector('.qty').value = 1;
    row.querySelector('.amount').value = 0;
    row.querySelector('.item-select').selectedIndex = 0;

    row.querySelector('.sub-item-select').innerHTML = '<option value="">-- Select Sub Item --</option>';
    document.querySelector('#itemsTable tbody').appendChild(row);
}

// Remove row
function removeRow(btn) {
    if (document.querySelectorAll('#itemsTable tbody tr').length > 1) {
        btn.closest('tr').remove();
        calculateAmounts();
    }
}

// Calculate totals
function calculateAmounts() {
    let total = 0;

    document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
        let qty = parseFloat(row.querySelector('.qty').value) || 0;
        let rate = parseFloat(row.querySelector('.rate').value) || 0;
        let amount = qty * rate;

        row.querySelector('.amount').value = amount.toFixed(2);
        total += amount;
    });

    document.getElementById('totalAmount').value = total.toFixed(2);
}

calculateAmounts();
document.querySelector('#itemsTable').addEventListener('input', calculateAmounts);
</script>

<?php include('./includes/footer.php'); ?>
