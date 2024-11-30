<?php
include '../connection.php';

if (isset($_GET['owner_id']) && isset($_GET['page'])) {
    $owner_id = $_GET['owner_id'];
    $page = $_GET['page'];

    // Debugging: Log the variables
    error_log("Owner ID: $owner_id, Page: $page");

    $limit = 10; // Pagination limit
    $offset = ($page - 1) * $limit;

    $countQuery = "SELECT COUNT(*) AS total FROM tbl_catch_report c 
                   INNER JOIN tbl_vessel v ON c.vessel_id = v.vessel_id
                   INNER JOIN tbl_owner o ON v.owner_id = o.owner_id
                   WHERE v.owner_id = '$owner_id'";

    $countResult = mysqli_query($conn, $countQuery);
    if (!$countResult) {
        error_log("Error in count query: " . mysqli_error($conn));
    }
    
    $countRow = mysqli_fetch_assoc($countResult);
    $totalRecords = $countRow['total'];
    $totalPages = ceil($totalRecords / $limit);

    error_log("Total Records: $totalRecords, Total Pages: $totalPages");

    // Query to fetch fish catch data
    $fishCatchData = "SELECT c.catch_id, v.vessel_name, c.depart_date, c.return_date
                      FROM tbl_catch_report c
                      INNER JOIN tbl_vessel v ON c.vessel_id = v.vessel_id
                      INNER JOIN tbl_owner o ON v.owner_id = o.owner_id
                      WHERE v.owner_id = '$owner_id'
                      LIMIT $limit OFFSET $offset";

    $result = mysqli_query($conn, $fishCatchData);
    if (!$result) {
        error_log("Error in catch data query: " . mysqli_error($conn));
    }

    $data = '';
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Format date fields
            $depart_date = date('F j, Y g:i A', strtotime($row['depart_date']));
            $return_date = date('F j, Y g:i A', strtotime($row['return_date']));
            
            $data .= "<tr>";
            $data .= "<td>" . htmlspecialchars($row['catch_id']) . "</td>";
            $data .= "<td>" . htmlspecialchars($row['vessel_name']) . "</td>";
            $data .= "<td>" . htmlspecialchars($depart_date) . "</td>";
            $data .= "<td>" . htmlspecialchars($return_date) . "</td>";
            $data .= "</tr>";
        }
    } else {
        $data .= "<tr><td colspan='4'>No catch report yet.</td></tr>";
    }

    // Pagination logic (only display if needed)
    $pagination = '';
    if ($totalPages > 1) {
        if ($page > 1) {
            $pagination .= '<a href="#" class="btn btn-primary pagination-link" data-page="' . ($page - 1) . '">Previous</a>';
        }

        for ($i = 1; $i <= $totalPages; $i++) {
            if ($i == $page) {
                $pagination .= '<span class="btn btn-secondary disabled">' . $i . '</span>';
            } else {
                $pagination .= '<a href="#" class="btn btn-primary pagination-link" data-page="' . $i . '">' . $i . '</a>';
            }
        }

        if ($page < $totalPages) {
            $pagination .= '<a href="#" class="btn btn-primary pagination-link" data-page="' . ($page + 1) . '">Next</a>';
        }
    }

    // Return the response as JSON
    echo json_encode([
        'data' => $data,
        'pagination' => ($totalPages > 1) ? $pagination : '' // Only include pagination if more than 1 page
    ]);
}

$conn->close();
?>
