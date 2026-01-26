<?php
include('./config/db.php');

$cid = intval($_GET['customer_id']);
$cloth_id = intval($_GET['cloth_id']);
$subtype_id = intval($_GET['subtype_id']);

$sql = "SELECT * FROM measurements WHERE customer_id=$cid AND garment_type_id=$cloth_id AND subtype_id=$subtype_id LIMIT 1";
$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    echo json_encode([
        'id' => $row['id'],
        'data' => json_decode($row['data'], true)
    ]);
} else {
    echo json_encode(['id' => null, 'data' => []]);
}
