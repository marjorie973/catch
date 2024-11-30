<?php
include '../connection.php';

if (isset($_GET['catch_id'])) {
    $catch_id = $_GET['catch_id'];

    $query = "SELECT fish_name, unit, price, quantity FROM tbl_catched_fish WHERE catch_id = '$catch_id'";
    $result = mysqli_query($conn, $query);

    $fishNames = [];

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $row['price'] = empty($row['price']) || $row['price'] == 0 ? 'Not yet set' : $row['price'];
            $row['quantity'] = empty($row['quantity']) || $row['quantity'] == 0 ? 'Not yet set' : $row['quantity'];
            $row['unit'] = empty($row['unit']) || $row['unit'] == 0 ? 'Not yet set' : $row['unit'];

            $fishNames[] = $row;
        }
    }

    echo json_encode($fishNames);
} else {
    echo json_encode([]);
}
?>