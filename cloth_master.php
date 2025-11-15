<?php
include('./includes/header.php');
include('./config/db.php');

// --- ADD or UPDATE Cloth Master ---
if (isset($_POST['save'])) {
    $id = $_POST['id'] ?? '';
    $title = trim($_POST['title']);
    $gender = $_POST['gender'];
    $note = trim($_POST['note']);

    if ($id == '') {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO cloth_types (title, gender, note) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $gender, $note);
        $msg = "Cloth Master Added Successfully!";
    } else {
        // UPDATE
        $stmt = $conn->prepare("UPDATE cloth_types SET title=?, gender=?, note=? WHERE id=?");
        $stmt->bind_param("sssi", $title, $gender, $note, $id);
        $msg = "Cloth Master Updated Successfully!";
    }

    if ($stmt->execute()) {
        echo "<script>alert('$msg'); window.location='cloth_master.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Error: {$stmt->error}</div>";
    }
}

// --- DELETE Cloth Master ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Check if this cloth_type_id exists in cloth_subtypes
    $check = $conn->query("SELECT COUNT(*) AS total FROM cloth_subtypes WHERE cloth_type_id = $id");
    $row = $check->fetch_assoc();

    if ($row['total'] > 0) {
        // Dependency found — don’t delete
        echo "<script>
            alert('⚠️ Please delete all subcategories under this cloth type before deleting it.');
            window.location='cloth_master.php';
        </script>";
    } else {
        // No dependency — safe to delete
        if ($conn->query("DELETE FROM cloth_types WHERE id=$id")) {
            echo "<script>
                alert('✅ Cloth Type deleted successfully!');
                window.location='cloth_master.php';
            </script>";
        } else {
            echo "<script>
                alert('❌ Error deleting record: {$conn->error}');
                window.location='cloth_master.php';
            </script>";
        }
    }
}



// --- EDIT MODE (Fetch Record) ---
$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM cloth_types WHERE id=$id");
    $editData = $res->fetch_assoc();
}

// --- FETCH ALL Cloth Masters ---
$result = $conn->query("SELECT * FROM cloth_types ORDER BY id DESC");
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3" style="padding-top:0.5rem;">
        <h3 class="fw-bold text-primary mb-0">🧵 Add / Edit Cloth Master</h3>
        <a href="index.php" class="btn btn-success fw-semibold shadow-sm">← Back to Dashboard</a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
           <form method="post">
    <input type="hidden" name="id" value="<?= $editData['id'] ?? '' ?>">

    <div class="row">
        <div class="col-md-4 mb-2">
            <label class="form-label fw-semibold">Title</label>
            <input type="text" name="title" class="form-control form-control-sm" required
                   value="<?= htmlspecialchars($editData['title'] ?? '') ?>">
        </div>

        <div class="col-md-3 mb-2">
            <label class="form-label fw-semibold">Gender</label>
            <select name="gender" class="form-control form-control-sm" required>
                <?php
                $genders = ['Male', 'Female', 'Unisex'];
                $selectedGender = $editData['gender'] ?? 'Unisex';
                foreach ($genders as $g) {
                    $sel = ($g == $selectedGender) ? 'selected' : '';
                    echo "<option value='$g' $sel>$g</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-4 mb-2">
            <label class="form-label fw-semibold">Note</label>
            <div class="d-flex align-items-start">
                <textarea name="note" class="form-control form-control-sm me-2" rows="1"><?= htmlspecialchars($editData['note'] ?? '') ?></textarea>
                <div class="col-md-2 mb-2">
                    <button type="submit" name="save" class="btn btn-success btn-sm mb-1 px-3">
                        💾 <?= isset($editData) ? 'Update' : 'Save' ?>
                    </button>
                    <?php if (isset($editData)): ?>
                        <a href="cloth_master.php" class="btn btn-secondary btn-sm px-3">Cancel</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</form>


        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <!-- <h5 class="fw-bold mb-3 text-secondary">📋 Cloth Master List</h5> -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" style="font-size:0.875rem;">
  <thead class="table-primary text-white">
    <tr>
      <th style="padding:0.35rem 0.5rem; vertical-align:middle;">#</th>
      <th style="padding:0.35rem 0.5rem; vertical-align:middle;">Title</th>
      <th style="padding:0.35rem 0.5rem; vertical-align:middle;">Gender</th>
      <th style="padding:0.35rem 0.5rem; vertical-align:middle;">Note</th>
      <th style="padding:0.35rem 0.5rem; vertical-align:middle;" width="150">Actions</th>
    </tr>
  </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): $i=1; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td style='padding:0.35rem 0.5rem; vertical-align:middle;'><?= $i++ ?></td>
                                    <td style='padding:0.35rem 0.5rem; vertical-align:middle;'><?= htmlspecialchars($row['title']) ?></td>
                                    <td style='padding:0.35rem 0.5rem; vertical-align:middle;'><?= htmlspecialchars($row['gender']) ?></td>
                                    <td style='padding:0.35rem 0.5rem; vertical-align:middle;'><?= nl2br(htmlspecialchars($row['note'])) ?></td>
                                    <td style='padding:0.35rem 0.5rem; vertical-align:middle;'>
                                        <a href="cloth_master.php?edit=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">✏️ Edit</a>
                                        <a href="cloth_master.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this record?')">🗑️ Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center text-muted">No records found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.card { border-radius: 10px; }
.table td, .table th { vertical-align: middle; }
</style>

<?php include('./includes/footer.php'); ?>
