<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection
include '../connection.php';

// Check if the form is submitted
if (isset($_POST['setPrice'])) {
    // Ensure the required fields are set
    if (isset($_POST['fish_id'], $_POST['fish_unit'], $_POST['fish_price'], $_POST['fish_quantity'])) {
        $fishId = $_POST['fish_id'];
        $catch_id = $_POST['catch_id_add'];
        $fishUnit = $_POST['fish_unit'];
        $fishPrice = $_POST['fish_price'];
        $fishQuantity = $_POST['fish_quantity'];
        // Prepare the SQL query
        $query = "UPDATE tbl_catched_fish SET unit = ?, price = ?, quantity = ? WHERE catched_fish_id = ? AND catch_id = ?;";

        // Prepare the statement
        if ($stmt = $conn->prepare($query)) {
            // Bind parameters and execute
            $stmt->bind_param("sdiii", $fishUnit, $fishPrice, $fishQuantity, $fishId, $catch_id);
            $stmt->execute();

            // Check if rows were affected
            if ($stmt->affected_rows > 0) {
                $_SESSION['redirectTo'] = 'pricing';
                header("Location: index.php");
                exit();
            } else {
                // Database operation failed
                echo "<script>
                    alert('Failed to update the price. Please try again.');
                    window.history.back(); // Go back to the previous page
                  </script>";
                exit;
            }

            $stmt->close();
        } else {
            error_log('Error preparing query: ' . $conn->error);
            echo "<script>
                alert('Database error occurred during query preparation.');
                window.history.back(); // Go back to the previous page
              </script>";
            exit;
        }
    } else {
        error_log('Missing POST parameters: ' . json_encode($_POST));
        echo "<script>
            alert('Required form data is missing.');
            window.history.back(); // Go back to the previous page
          </script>";
        exit;
    }
} else {
    error_log('Invalid request: ' . json_encode($_POST));
    echo "<script>
        alert('Invalid request.');
        window.history.back(); // Go back to the previous page
      </script>";
    exit;
}

$conn->close();
?>