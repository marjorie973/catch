<?php 
include '../connection.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $owner_id = mysqli_real_escape_string($conn, $_POST['owner_id']);
    $vessel_name = mysqli_real_escape_string($conn, $_POST['vessel_name']);
    $vessel_origin = mysqli_real_escape_string($conn, $_POST['origin']);

    $vessel_id = rand(10000000, 99999999);

    $insertQuery = "INSERT INTO tbl_vessel (vessel_id, vessel_name, vessel_origin, owner_id) 
                    VALUES ('$vessel_id', '$vessel_name', '$vessel_origin', '$owner_id')";

    if (mysqli_query($conn, $insertQuery)) {
        $_SESSION['redirectTo'] = 'vessels';
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
