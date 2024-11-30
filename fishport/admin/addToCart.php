<?php
include '../connection.php';  // Ensure the connection is included

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input values from POST request
    $catched_fish_id = isset($_POST['catched_fish_id']) ? (int)$_POST['catched_fish_id'] : 0;
    $sell_id = isset($_POST['sell_id']) ? $_POST['sell_id'] : '';
    $quantityBought = isset($_POST['quantityBought']) ? (int)$_POST['quantityBought'] : 0;

    // Check if the values are valid
    if ($catched_fish_id > 0 && !empty($sell_id) && $quantityBought > 0) {
        // First, check if the same catched_fish_id and sell_id already exist in tbl_sell_fish_list
        $checkExist = "SELECT buy_quantity FROM tbl_sell_fish_list WHERE catched_fish_id = $catched_fish_id AND sell_id = '$sell_id'";
        $result = mysqli_query($conn, $checkExist);

        if (mysqli_num_rows($result) > 0) {
            // If the record exists, update the quantity
            $row = mysqli_fetch_assoc($result);
            $existingQuantity = $row['buy_quantity'];
            $newQuantity = $existingQuantity + $quantityBought;

            // Update the quantity in tbl_sell_fish_list
            $sqlUpdate = "UPDATE tbl_sell_fish_list SET buy_quantity = $newQuantity WHERE catched_fish_id = $catched_fish_id AND sell_id = '$sell_id'";
            if (mysqli_query($conn, $sqlUpdate)) {
                // Update the quantity in tbl_catched_fish
                $sqlUpdateFish = "UPDATE tbl_catched_fish SET quantity = quantity - $quantityBought WHERE catched_fish_id = $catched_fish_id";
                if (mysqli_query($conn, $sqlUpdateFish)) {
                    $_SESSION['redirectTo'] = 'selling'; 
                    header("Location: index.php");
                    exit();
                } else {
                    echo "Error updating tbl_catched_fish: " . mysqli_error($conn);
                }
            } else {
                echo "Error updating tbl_sell_fish_list: " . mysqli_error($conn);
            }
        } else {
            $sell_fish_list_id = rand(10000000, 99999999);

            $sqlInsert = "INSERT INTO tbl_sell_fish_list (sell_fish_list_id, catched_fish_id, sell_id, buy_quantity) 
                          VALUES ($sell_fish_list_id, $catched_fish_id, '$sell_id', $quantityBought)";
            if (mysqli_query($conn, $sqlInsert)) {
                // Update the quantity in tbl_catched_fish
                $sqlUpdateFish = "UPDATE tbl_catched_fish SET quantity = quantity - $quantityBought WHERE catched_fish_id = $catched_fish_id";
                if (mysqli_query($conn, $sqlUpdateFish)) {
                    $_SESSION['redirectTo'] = 'selling'; 
                    header("Location: index.php");
                    exit();
                } else {
                    echo "Error updating tbl_catched_fish: " . mysqli_error($conn);
                }
            } else {
                echo "Error inserting into tbl_sell_fish_list: " . mysqli_error($conn);
            }
        }
    } else {
        echo "Invalid input values.";
    }
}

// Close the connection
mysqli_close($conn);
?>
