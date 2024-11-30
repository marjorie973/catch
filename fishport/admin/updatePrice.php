<?php
include('../connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the data from the form submission
    $fishId = $_POST['fish_id'];
    $unit = $_POST['unit'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $catch_id = $_POST['catchIDEdit'];

    if (!empty($fishId) && !empty($unit) && !empty($quantity) && !empty($price) && !empty($catch_id)) {

        $sql_update = "UPDATE tbl_catched_fish SET unit = ?, quantity = ?, price = ? WHERE catched_fish_id = ? AND catch_id = ?";
        if ($stmt_update = $conn->prepare($sql_update)) {
            $stmt_update->bind_param("sidii", $unit, $quantity, $price, $fishId, $catch_id);
            if ($stmt_update->execute()) {
                $_SESSION['redirectTo'] = 'pricing';
                header("Location: index.php");
                exit();
            } else {
                echo "<script>
                        alert('Failed to update the price. Please try again.');
                        window.history.back();
                    </script>";
                exit;
            }
        } else {
            echo "<script>
                    alert('Failed to prepare the update SQL statement.');
                    window.history.back();
                </script>";
            exit;
        }
    } else {
        echo "<script>
            alert('Please fill in all fields.');
            window.history.back();
        </script>";
        exit;
    }
} else {
    echo "<script>
        alert('Invalid request method.');
        window.history.back();
    </script>";
    exit;
}
