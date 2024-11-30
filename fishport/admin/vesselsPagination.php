<?php
include '../connection.php';

header('Content-Type: application/json');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

$offset = ($page - 1) * $limit;

$getVessels = "SELECT v.vessel_id, o.owner_lname, o.owner_fname, v.vessel_name, v.vessel_origin
               FROM tbl_vessel v
               LEFT JOIN tbl_owner o ON v.owner_id = o.owner_id
               LIMIT $limit OFFSET $offset";

$resultVessels = mysqli_query($conn, $getVessels);

$totalQuery = "SELECT COUNT(*) AS total FROM tbl_vessel";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $limit);

$tableData = '';

if ($resultVessels && mysqli_num_rows($resultVessels) > 0) {
    while ($row = mysqli_fetch_assoc($resultVessels)) {
        $ownerName = $row['owner_fname'] . ' ' . $row['owner_lname'];
        $tableData .= "<tr>
                        <td>" . $row['vessel_id'] . "</td>
                        <td>" . $ownerName . "</td>
                        <td>" . $row['vessel_name'] . "</td>
                        <td>" . $row['vessel_origin'] . "</td>
                    </tr>";
    }
} else {
    // If no vessels found, display this message
    $tableData = "<tr><td colspan='4' class='text-center'>No vessels found</td></tr>";
}

// Generate pagination links
$paginationLinks = '';
for ($i = 1; $i <= $totalPages; $i++) {
    $activeClass = ($i == $page) ? 'active' : '';
    $paginationLinks .= "<li class='page-item $activeClass'>
                            <a class='page-link' href='#' data-page='$i'>$i</a>
                          </li>";
}

echo json_encode([
    'tableData' => $tableData, 
    'paginationLinks' => $paginationLinks
]);
?>
