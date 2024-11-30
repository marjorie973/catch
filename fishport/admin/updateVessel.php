<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $vessel_id = isset($_POST['vessel_id']) ? (int)$_POST['vessel_id'] : 0;
    $vessel_name = isset($_POST['vessel_name']) ? mysqli_real_escape_string($conn, trim($_POST['vessel_name'])) : '';
    $origin = isset($_POST['origin']) ? mysqli_real_escape_string($conn, trim($_POST['origin'])) : '';

    if ($vessel_id > 0 && !empty($vessel_name) && !empty($origin)) {

        $updateQuery = "
            UPDATE tbl_vessel
            SET vessel_name = '$vessel_name', vessel_origin = '$origin' 
            WHERE vessel_id = $vessel_id
        ";

        if (mysqli_query($conn, $updateQuery)) {
            $_SESSION['redirectTo'] = 'vessels';
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = 'Failed to update vessel details.';
            header("Location: index.php");
            exit();
        }

    } else {
        $_SESSION['error'] = 'All fields are required and must be valid.';
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
