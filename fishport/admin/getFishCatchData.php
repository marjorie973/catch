<?php
include '../connection.php';

if (isset($_GET['owner_id'])) {
    $owner_id = $_GET['owner_id'];
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $countQuery = "SELECT COUNT(*) as total FROM tbl_catch_report c
                   INNER JOIN tbl_vessel v ON c.vessel_id = v.vessel_id
                   INNER JOIN tbl_owner o ON v.owner_id = o.owner_id
                   WHERE o.owner_id = ?";
    if ($countStmt = $conn->prepare($countQuery)) {
        $countStmt->bind_param("i", $owner_id);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $countRow = $countResult->fetch_assoc();
        $totalRecords = $countRow['total'];
        $countStmt->close();
    } else {
        echo json_encode([]); 
        exit();
    }

    // Fetch the paginated data
    $query = "SELECT c.catch_id, v.vessel_name, c.depart_date, c.return_date
              FROM tbl_catch_report c
              INNER JOIN tbl_vessel v ON c.vessel_id = v.vessel_id
              INNER JOIN tbl_owner o ON v.owner_id = o.owner_id
              WHERE o.owner_id = ?
              LIMIT ? OFFSET ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("iii", $owner_id, $limit, $offset);
        $stmt->execute();

        $result = $stmt->get_result();
        $catchData = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['depart_date'] = formatDate($row['depart_date']);
                $row['return_date'] = formatDate($row['return_date']);
                $catchData[] = $row;
            }
        }

        $paginationInfo = [
            'data' => $catchData,
            'totalRecords' => $totalRecords,
            'totalPages' => ceil($totalRecords / $limit),
            'currentPage' => $page
        ];

        echo json_encode($paginationInfo);

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}

$conn->close();

function formatDate($date) {
    $dateTime = new DateTime($date);
    return $dateTime->format('F j, Y g:i A');
}
?>
