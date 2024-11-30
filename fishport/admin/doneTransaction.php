<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $owner_id = isset($_POST['owner_id']) ? $_POST['owner_id'] : '';

    if (!empty($owner_id)) {
        $sql_update_status = "UPDATE tbl_owner SET status = '' WHERE owner_id = '$owner_id'";

        if (mysqli_query($conn, $sql_update_status)) {
            header("Location: index.php");
            exit();
        } else {
            echo "<p>Error updating status: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p>Owner ID is missing.</p>";
    }
}
?>
