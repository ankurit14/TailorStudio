<?php
include('./config/db.php');
include('./includes/header.php');
?>

<!-- Page Title Section -->
<div class="d-flex justify-content-between align-items-center mt-4 mb-3" style="padding-top:0.5rem;">
    <h3 class="fw-bold text-primary mb-0">
        🧑‍🔧 Karigar List
    </h3>
    <a href="karigar_add.php" class="btn btn-success fw-semibold shadow-sm">
        ➕ Add New Karigar
    </a>
</div>

<!-- Karigar Table Card -->
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body">
        <div class="table-responsive">
           <table class="table table-bordered table-hover align-middle" style="font-size:0.875rem;">
  <thead class="table-primary text-white">
                    <tr>
                        <th style="padding:0.35rem 0.5rem; vertical-align:middle; width: 60px;">#</th>
                        <th style="padding:0.35rem 0.5rem; vertical-align:middle; width: 200px;">Name</th>
                        <th style="padding:0.35rem 0.5rem; vertical-align:middle; width: 150px;">Mobile</th>
                        <th style="padding:0.35rem 0.5rem; vertical-align:middle; width: 250px;">Skill / Specialization</th>
                        <th style="padding:0.35rem 0.5rem; vertical-align:middle; width: 180px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = $conn->query("SELECT * FROM karigars ORDER BY id DESC");
                    $i = 1;

                    if ($res->num_rows > 0) {
                        while ($r = $res->fetch_assoc()) {
                            echo '
                            <tr>
                                <td style="padding:0.35rem 0.5rem; vertical-align:middle;">' . $i . '</td>
                                <td class="text-start" style="padding:0.35rem 0.5rem; vertical-align:middle;">' . htmlspecialchars($r['name']) . '</td>
                                <td style="padding:0.35rem 0.5rem; vertical-align:middle;">' . htmlspecialchars($r['mobile']) . '</td>
                                <td class="text-start" style="padding:0.35rem 0.5rem; vertical-align:middle;">' . htmlspecialchars($r['specialization']) . '</td>
                                <td style="padding:0.35rem 0.5rem; vertical-align:middle;">
                                    <a href="karigar_edit.php?id=' . $r['id'] . '" 
                                       class="btn btn-sm btn-outline-info me-2 shadow-sm"
                                       title="Edit Karigar">
                                       ✏️ Edit
                                    </a>
                                    <a href="karigar_delete.php?id=' . $r['id'] . '" 
                                       class="btn btn-sm btn-outline-danger shadow-sm"
                                       title="Delete Karigar"
                                       onclick="return confirm(\'Are you sure you want to delete this karigar?\')">
                                       🗑️ Delete
                                    </a>
                                </td>
                            </tr>';
                            $i++;
                        }
                    } else {
                        echo '<tr><td colspan="5" class="text-center text-muted py-3">No karigars found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Inline CSS for Consistent Styling -->
<style>
    /* Table styling */
    table.table {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        overflow: hidden;
        font-size: 15px;
    }

    thead tr th {
        font-weight: 600;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }

    tbody tr:hover {
        background-color: #f8f9fa !important;
        transition: background-color 0.2s ease-in-out;
    }

    /* Button styling */
    .btn-outline-info {
        color: #0dcaf0;
        border-color: #0dcaf0;
        transition: all 0.2s ease-in-out;
    }
    .btn-outline-info:hover {
        background-color: #0dcaf0;
        color: #fff;
    }

    .btn-outline-danger {
        color: #dc3545;
        border-color: #dc3545;
        transition: all 0.2s ease-in-out;
    }
    .btn-outline-danger:hover {
        background-color: #dc3545;
        color: #fff;
    }

    .card-body {
        padding: 1.5rem 1.5rem 1rem;
    }
</style>

<?php include('./includes/footer.php'); ?>
