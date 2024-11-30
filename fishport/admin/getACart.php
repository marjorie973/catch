<?php
include '../connection.php';

$sell_id = rand(10000000, 99999999);

$query = "INSERT INTO tbl_sell (sell_id, status) VALUES ('$sell_id', 'On Cart')";
$result = mysqli_query($conn, $query);

if ($result) {
    $_SESSION['redirectTo'] = 'selling'; 
    header("Location: index.php");
    exit();
} else {
    echo "Error: Could not create the cart.";
}
?>
