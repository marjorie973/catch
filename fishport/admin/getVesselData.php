<?php
include '../connection.php';

// Check if vessel_id is set
if (isset($_GET['vessel_id'])) {
    $vessel_id = $_GET['vessel_id'];

    // Query to fetch vessel and owner data
    $query = "
        SELECT 
            v.vessel_name, 
            v.vessel_origin, 
            CONCAT(o.owner_fname, ' ', o.owner_lname) AS owner_name, 
            v.owner_id
        FROM tbl_vessel v
        LEFT JOIN tbl_owner o ON v.owner_id = o.owner_id
        WHERE v.vessel_id = $vessel_id
    ";

    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);

        // Prepare the data to be returned as JSON
        $response = [
            'vessel_name' => $row['vessel_name'],
            'vessel_origin' => $row['vessel_origin'],
            'owner_id' => $row['owner_id']
        ];

        // Return the response as JSON
        echo json_encode($response);
    } else {
        echo json_encode(['error' => 'Vessel not found']);
    }
} else {
    echo json_encode(['error' => 'Vessel ID not provided']);
}
?>
