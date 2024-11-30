<?php
session_start();
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $phonenumber = $_POST['phonenumber'];

    if ($_SESSION["userlogin"] == "admin") {
        $sql = "UPDATE tbl_broker SET username = ?, password = ?, fname = ?, mname = ?, lname = ?, address = ?, phonenum = ? WHERE broker_id = ?";
    } else {
        $sql = "UPDATE tbl_owner SET username = ?, password = ?, owner_fname = ?, owner_mname = ?, owner_lname = ?, address = ?, phonenum = ? WHERE owner_id = ?";
    }

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        $_SESSION['error'] = "Error preparing statement: " . $conn->error;
        header("Location: index.php");
        exit();
    }

    if ($_SESSION["userlogin"] == "admin") {
        $stmt->bind_param("ssssssss", $username, $password, $firstname, $middlename, $lastname, $address, $phonenumber, $id);
    } else {
        $stmt->bind_param("ssssssss", $username, $password, $firstname, $middlename, $lastname, $address, $phonenumber, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['message'] = "Settings saved successfully!";
    } else {
        $_SESSION['error'] = "Error updating settings: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    $_SESSION['redirectTo'] = 'settings';
    header("Location: index.php");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>
