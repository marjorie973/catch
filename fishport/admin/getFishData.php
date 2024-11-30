<?php
include '../connection.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['catch_id'])) {
    $catch_id = $_GET['catch_id'];
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $query = "SELECT catched_fish_id, catch_id, fish_name, unit, price, quantity  
              FROM tbl_catched_fish WHERE catch_id = ? 
              LIMIT ? OFFSET ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("iii", $catch_id, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        $fishData = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['price'] = empty($row['price']) || $row['price'] == 0 ? 'Not yet set' : $row['price'];
                $row['quantity'] = empty($row['quantity']) || $row['quantity'] == 0 ? 'Not yet set' : $row['quantity'];
                $row['unit'] = empty($row['unit']) || $row['unit'] == 0 ? 'Not yet set' : $row['unit'];
                $row['catched_fish_id'] = empty($row['catched_fish_id']) || $row['catched_fish_id'] == 0 ? 'Not yet set' : $row['catched_fish_id'];
                $row['catch_id'] = empty($row['catch_id']) || $row['catch_id'] == 0 ? 'Not yet set' : $row['catch_id'];

                $fishData[] = $row;
            }
        }

        $countQuery = "SELECT COUNT(*) AS total FROM tbl_catched_fish WHERE catch_id = ?";
        if ($countStmt = $conn->prepare($countQuery)) {
            $countStmt->bind_param("i", $catch_id);
            $countStmt->execute();
            $countResult = $countStmt->get_result();
            $totalRecords = 0;

            if ($countResult->num_rows > 0) {
                $totalRecords = $countResult->fetch_assoc()['total'];
            }

            $countStmt->close();
        }

        $totalPages = ceil($totalRecords / $limit);

        // Return the fish data along with pagination info
        $paginationInfo = [
            'data' => $fishData,
            'totalRecords' => $totalRecords,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ];

        echo json_encode($paginationInfo);

        $stmt->close();
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}

$conn->close();
