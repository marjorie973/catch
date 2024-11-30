<?php
date_default_timezone_set('Asia/Manila');

session_start();
$conn = mysqli_connect("localhost", "root", "", "fishport");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>