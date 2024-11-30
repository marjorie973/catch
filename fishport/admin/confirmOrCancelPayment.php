<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sell_id = isset($_POST['sell_id']) ? $_POST['sell_id'] : '';

    if (!empty($sell_id)) {
        $getCartList = "SELECT catched_fish_id, buy_quantity FROM tbl_sell_fish_list WHERE sell_id = '$sell_id'";
        $result = mysqli_query($conn, $getCartList);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $catched_fish_id = $row['catched_fish_id'];
                $quantity = $row['buy_quantity'];

                $updateQuantity = "UPDATE tbl_catched_fish 
                                   SET quantity = quantity + $quantity 
                                   WHERE catched_fish_id = '$catched_fish_id'";
                mysqli_query($conn, $updateQuantity);
            }

            $deleteCartItems = "DELETE FROM tbl_sell_fish_list WHERE sell_id = '$sell_id'";
            mysqli_query($conn, $deleteCartItems);

            

            echo "Cancel Successfully!";
        } else {
            echo "No cart items found for this sell_id.";
        }

        $deleteSell = "DELETE FROM tbl_sell WHERE sell_id = '$sell_id'";
        mysqli_query($conn, $deleteSell);
    } else {
        echo "Invalid sell_id.";
    }

    mysqli_close($conn);
} else {
    echo "Invalid request method.";
}
?>
