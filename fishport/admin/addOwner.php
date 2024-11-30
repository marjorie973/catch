<?php
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $phonenumber = $_POST['phonenumber'];
    $address = $_POST['address'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $broker_id = bin2hex(random_bytes(8));

    $sql = "INSERT INTO tbl_owner (owner_id, owner_lname, owner_fname, owner_mname, phonenum, address, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "ssssssss", $broker_id, $lastname, $firstname, $middlename, $phonenumber, $address, $username, $password);

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
