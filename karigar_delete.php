<?php
include('./config/db.php');
include('./includes/header.php');

// ✅ Validate the ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>window.location='karigars.php';</script>";
    exit;
}

$id = intval($_GET['id']);

// ✅ Step 1: Check if karigar is used in orders
$check = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE karigar_id = $id");
$row = $check->fetch_assoc();

if ($row['total'] > 0) {
    // ⚠️ Karigar used in orders — don't delete
    echo "<script>
        alert('⚠️ This Karigar is already linked to existing orders. Please delete or reassign those orders first.');
        window.location='karigars.php';
    </script>";
    exit;
}

// ✅ Step 2: Safe to delete
$stmt = $conn->prepare("DELETE FROM karigars WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>
        alert('✅ Karigar deleted successfully!');
        window.location='karigars.php';
    </script>";
} else {
    echo "<script>
        alert('❌ Error deleting Karigar: {$conn->error}');
        window.location='karigars.php';
    </script>";
}

$stmt->close();
?>
