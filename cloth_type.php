<?php
include('./includes/header.php');
include('./config/db.php');

// -----------------------------
// Handle Add/Edit Subtype
// -----------------------------
if (isset($_POST['save_subtype'])) {
    $id = $_POST['id'] ?? '';
    $cloth_type_id = intval($_POST['cloth_type_id']);
    $title = trim($_POST['title']);
    $note = trim($_POST['note']);
    $image = '';

    // Handle image upload if any
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/subtypes/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
        $image = $fileName;
    }

    if ($id == '') {
        // Insert new subtype
        $stmt = $conn->prepare("INSERT INTO cloth_subtypes (cloth_type_id, title, image, note) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $cloth_type_id, $title, $image, $note);
        $msg = $stmt->execute() ? "Cloth Subtype Added Successfully" : "Error: " . $stmt->error;
    } else {
        // Update existing subtype
        if ($image) {
            $stmt = $conn->prepare("UPDATE cloth_subtypes SET cloth_type_id=?, title=?, image=?, note=? WHERE id=?");
            $stmt->bind_param("isssi", $cloth_type_id, $title, $image, $note, $id);
        } else {
            $stmt = $conn->prepare("UPDATE cloth_subtypes SET cloth_type_id=?, title=?, note=? WHERE id=?");
            $stmt->bind_param("issi", $cloth_type_id, $title, $note, $id);
        }
        $msg = $stmt->execute() ? "Cloth Subtype Updated Successfully" : "Error: " . $stmt->error;
    }
    echo "<script>alert('$msg');window.location='cloth_type.php';</script>";
}

// -----------------------------
// Fetch for Edit
// -----------------------------
$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $editData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM cloth_subtypes WHERE id=$id"));
}


// -----------------------------
// Delete Subtype (Safe Delete)
// -----------------------------
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // ✅ Step 1: Check if this subtype is used in measurements
    $check = $conn->query("SELECT COUNT(*) AS total FROM measurements WHERE subtype_id = $id");
    $row = $check->fetch_assoc();

    if ($row['total'] > 0) {
        // ⚠️ Subtype in use — don’t delete
        echo "<script>
            alert('⚠️ This subcategory is already used in measurements. Please delete related measurements first.');
            window.location='cloth_type.php';
        </script>";
    } else {
        // ✅ Safe to delete
        if ($conn->query("DELETE FROM cloth_subtypes WHERE id = $id")) {
            echo "<script>
                alert('✅ Cloth Subtype deleted successfully!');
                window.location='cloth_type.php';
            </script>";
        } else {
            echo "<script>
                alert('❌ Error deleting subcategory: {$conn->error}');
                window.location='cloth_type.php';
            </script>";
        }
    }
}



// -----------------------------
// Fetch cloth types for dropdown
// -----------------------------
$clothTypes = mysqli_query($conn, "SELECT id, title FROM cloth_types ORDER BY title ASC");
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary mb-0">👕 Add / Edit Cloth Subtype</h3>
        <div class="d-flex gap-2">
            <a href="measurements_master.php" class="btn btn-success fw-semibold shadow-sm">📏 Manage Measurements</a>
            <!-- <a href="cloth_type.php" class="btn btn-secondary fw-semibold shadow-sm">↩️ Back to Cloth Types</a> -->
        </div>
    </div>

    <!-- Form -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="post" enctype="multipart/form-data" class="row g-3">
                <input type="hidden" name="id" value="<?= $editData['id'] ?? '' ?>">

                <!-- Parent Type -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Main Cloth Type</label>
                    <select name="cloth_type_id" class="form-control" required>
                        <option value="">-- Select Type --</option>
                        <?php while ($type = mysqli_fetch_assoc($clothTypes)): ?>
                            <option value="<?= $type['id'] ?>"
                                <?= isset($editData['cloth_type_id']) && $editData['cloth_type_id'] == $type['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['title']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Subtype Title -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Subtype Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Formal Shirt" required
                        value="<?= $editData['title'] ?? '' ?>">
                </div>

                <!-- Image -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Image</label>
                    <input type="file" name="image" class="form-control">
                    <?php if (!empty($editData['image'])): ?>
                        <img src="uploads/subtypes/<?= htmlspecialchars($editData['image']) ?>" width="70" class="mt-2 rounded shadow-sm">
                    <?php endif; ?>
                </div>

                <!-- Note -->
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Note</label>
                    <textarea name="note" class="form-control" rows="2"
                        placeholder="Optional note"><?= $editData['note'] ?? '' ?></textarea>
                </div>

                <div class="col-12 text-end mt-3">
                    <button type="submit" name="save_subtype" class="btn btn-success px-4">💾 Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Available Cloth Subtypes</h5>
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Subtype Title</th>
                        <th>Main Cloth Type</th>
                        <th>Image</th>
                        <th>Note</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = mysqli_query($conn, "
                        SELECT s.*, t.title AS cloth_type_title
                        FROM cloth_subtypes s
                        LEFT JOIN cloth_types t ON s.cloth_type_id = t.id
                        ORDER BY s.id DESC
                    ");
                    $i = 1;
                    while ($row = mysqli_fetch_assoc($res)):
                    ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['cloth_type_title']) ?></td>
                            <td>
                                <?php if (!empty($row['image'])): ?>
                                    <img src="uploads/subtypes/<?= htmlspecialchars($row['image']) ?>" width="60" class="rounded shadow-sm">
                                <?php else: ?>
                                    <span class="text-muted">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['note']) ?></td>
                            <td>
                                <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">✏️ Edit</a>
                                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Delete this subtype?')">🗑️ Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.card { border-radius: 10px; }
img { object-fit: cover; }
</style>

<?php include('./includes/footer.php'); ?>
