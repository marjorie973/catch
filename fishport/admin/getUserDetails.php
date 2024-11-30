<?php 
$id = $_SESSION["id"];
$firstname = "";
$middlename = "";
$lastname = "";
$username = "";
$password = "";
$address = "";
$phonenumber = "";
$whoLogIn = "";

if ($_SESSION["userlogin"] == "admin") {
    $sql = "SELECT broker_id, lname AS lastname, fname AS firstname, mname AS middlename, phonenum, address, username, password FROM tbl_broker WHERE broker_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $whoLogIn = "Admin";
} else {
    $sql = "SELECT owner_id, owner_lname AS lastname, owner_fname AS firstname, owner_mname AS middlename, phonenum, address, username, password FROM tbl_owner WHERE owner_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $whoLogIn = "Owner"; 
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $firstname = $row['firstname'];
    $middlename = $row['middlename'];
    $lastname = $row['lastname'];
    $username = $row['username'];
    $password = $row['password'];
    $address = $row['address'];
    $phonenumber = $row['phonenum'];
} else {
    echo "No records found.";
}

$stmt->close();
?>
