<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sell_id = isset($_POST['sell_id']) ? $_POST['sell_id'] : '';
    $buyer_name = isset($_POST['buyer_name']) ? $_POST['buyer_name'] : '';
    $buyer_address = isset($_POST['buyer_address']) ? $_POST['buyer_address'] : '';
    $buyer_phone = isset($_POST['buyer_phone']) ? $_POST['buyer_phone'] : '';
    $total_price = isset($_POST['total_price']) ? (float)$_POST['total_price'] : 0.00;

    if (!empty($sell_id) && !empty($buyer_name) && !empty($buyer_address) && !empty($buyer_phone)) {
        $updateSell = "UPDATE tbl_sell 
                       SET buyer_name = '$buyer_name', 
                           buyer_address = '$buyer_address', 
                           buyer_phonenumber = '$buyer_phone', 
                           total_price = $total_price, 
                           status = 'Paid'  
                       WHERE sell_id = '$sell_id'";

        if (mysqli_query($conn, $updateSell)) {
            echo "Payment confirmed successfully!";
        } else {
            echo "Error updating tbl_sell: " . mysqli_error($conn);
        }
    } else {
        echo "Please provide all required fields.";
    }

    mysqli_close($conn);
} else {
    echo "Invalid request method.";
}
?>
