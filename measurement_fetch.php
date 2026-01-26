<?php
include('./config/db.php');

$where = "WHERE 1";

// ✅ Filters
if (!empty($_POST['customer_id'])) {
    $cid = intval($_POST['customer_id']);
    $where .= " AND m.customer_id = $cid";
}

// Garment type filter
if (!empty($_POST['garment_type'])) {
    $subtype = intval($_POST['garment_type']);
    $where .= " AND m.subtype_id = $subtype";
}

// Date range filter
if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
    $from = $conn->real_escape_string($_POST['from_date']);
    $to = $conn->real_escape_string($_POST['to_date']);
    $where .= " AND (m.date_taken BETWEEN '$from' AND '$to')";
}

// ✅ Mobile filter
if (!empty($_POST['mobile'])) {
    $mobile = $conn->real_escape_string($_POST['mobile']);
    $where .= " AND c.mobile LIKE '%$mobile%'";
}


// ✅ Correct query with proper joins
$query = "
SELECT 
    m.*, 
    c.name AS customer_name,
    c.mobile AS customer_mobile,
    st.title AS subtype_title,
    st.image AS subtype_image,
    ct.title AS cloth_type_title
FROM measurements m
LEFT JOIN customers c ON m.customer_id = c.id
LEFT JOIN cloth_subtypes st ON m.subtype_id = st.id
LEFT JOIN cloth_types ct ON st.cloth_type_id = ct.id
$where
ORDER BY m.id DESC
";

$res = $conn->query($query);

if (!$res || $res->num_rows == 0) {
    echo '<div class="alert alert-warning m-2">No measurements found.</div>';
    exit;
}

echo '
<table class="table table-bordered table-striped align-middle" style="font-size:0.875rem;">
  <thead class="table-primary">
    <tr>
      <th style="padding:0.35rem 0.5rem; vertical-align:middle;">#</th>
      <th style="padding:0.35rem 0.5rem; vertical-align:middle;">Customer</th>
      <th style="padding:0.35rem 0.5rem; vertical-align:middle;">Mobile</th>
      <th style="padding:0.35rem 0.5rem; vertical-align:middle;">Garment Type</th>
      <th style="padding:0.35rem 0.5rem; vertical-align:middle;">Sub Type</th>
      <th style="padding:0.35rem 0.5rem; vertical-align:middle;">Date</th>
      <th style="padding:0.35rem 0.5rem; vertical-align:middle;">Actions</th>
    </tr>
  </thead>
  <tbody>
';



$i = 1;
while ($r = $res->fetch_assoc()) {
    $safeJson = htmlspecialchars(
        json_encode(json_decode($r['data']), JSON_UNESCAPED_UNICODE),
        ENT_QUOTES,
        'UTF-8'
    );

    $cust = htmlspecialchars($r['customer_name']);
    $mobile = htmlspecialchars($r['customer_mobile']);
    $clothType = htmlspecialchars($r['cloth_type_title']);
    $subType = htmlspecialchars($r['subtype_title']);
    $date = htmlspecialchars($r['date_taken']);
    $image = htmlspecialchars($r['subtype_image']);

    echo "
<tr>
  <td style='padding:0.35rem 0.5rem; vertical-align:middle;'>{$i}</td>
  <td style='padding:0.35rem 0.5rem; vertical-align:middle;'>{$cust}</td>
  <td style='padding:0.35rem 0.5rem; vertical-align:middle;'>{$mobile}</td>
  <td style='padding:0.35rem 0.5rem; vertical-align:middle;'>{$clothType}</td>
  <td style='padding:0.35rem 0.5rem; vertical-align:middle;'>{$subType}</td>
  <td style='padding:0.35rem 0.5rem; vertical-align:middle;'>{$date}</td>
  <td style='padding:0.35rem 0.5rem; vertical-align:middle;'>
    <button class='btn btn-sm btn-info text-white view-btn'
      style='padding:0.2rem 0.4rem; font-size:0.75rem;'
      data-json='{$safeJson}'
      data-garment='{$subType}'
      data-customer='{$cust}'
      data-mobile='{$mobile}'
      data-date='{$date}'
      data-image='{$image}'>👁️ View</button>
    <a href='measurement_edit.php?id={$r['id']}' class='btn btn-sm btn-warning' style='padding:0.2rem 0.4rem; font-size:0.75rem;'>✏️ Edit</a>
    <a href='measurement_print.php?id={$r['id']}' target='_blank' class='btn btn-sm btn-secondary' style='padding:0.2rem 0.4rem; font-size:0.75rem;'>🖨️ Print</a>
    <a href='measurement_delete.php?id={$r['id']}' onclick='return confirm(\"Delete this measurement?\")' class='btn btn-sm btn-danger' style='padding:0.2rem 0.4rem; font-size:0.75rem;'>🗑️ Delete</a>
  </td>
</tr>
";

    $i++;
}

echo '</tbody></table>';
?>
