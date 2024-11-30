<?php
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $owner_id = $_POST['owner_id'];
    $first_name = $_POST['firstname'];
    $middle_name = $_POST['middlename'];
    $last_name = $_POST['lastname'];
    $phone = $_POST['phonenumber'];
    $address = $_POST['address'];

    $sql = "UPDATE tbl_owner SET owner_fname = ?, owner_mname = ?, owner_lname = ?, phonenum = ?, address = ? WHERE owner_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "ssssss", $first_name, $middle_name, $last_name, $phone, $address, $owner_id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['redirectTo'] = 'usermanager';
        header("Location: index.php");
        exit();
    } else {
        $alertMessage = "Error: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
