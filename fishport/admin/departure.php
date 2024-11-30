<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['departure'])) {
        $catch_id = generateRandomString(8);

        $vessel_id = isset($_POST['vessel_id']) ? $_POST['vessel_id'] : '';

        $depart_date = date('Y-m-d H:i:s'); 

        $sql_insert_catch_report = "INSERT INTO tbl_catch_report (catch_id, vessel_id, depart_date) 
                                    VALUES ('$catch_id', '$vessel_id', '$depart_date')";

        if (mysqli_query($conn, $sql_insert_catch_report)) {
            $owner_id = $_SESSION['id'];

            $sql_update_status = "UPDATE tbl_owner SET status = 'Departure' WHERE owner_id = '$owner_id'";

            if (mysqli_query($conn, $sql_update_status)) {
                echo "<script>
                        window.location.href = 'index.php';
                        setTimeout(function() {
                            sidebarClick('transaction');
                        }, 500); // Delay to ensure the page has loaded
                      </script>";
                exit;
            } else {
                echo "<p>Error updating status in tbl_owner: " . mysqli_error($conn) . "</p>";
            }
        } else {
            echo "<p>Error inserting into tbl_catch_report: " . mysqli_error($conn) . "</p>";
        }
    } 
    
    
    if (isset($_POST['return'])) {
        $status = "Return";
        $return_date = date('Y-m-d H:i:s');
        $catch_id = isset($_POST['catch_id']) ? $_POST['catch_id'] : '';

        if ($catch_id) {
            $sql_update_catch_report = "UPDATE tbl_catch_report SET return_date = '$return_date' WHERE catch_id = '$catch_id'";

            if (mysqli_query($conn, $sql_update_catch_report)) {
                $owner_id = $_SESSION['id'];
                $sql_update_status = "UPDATE tbl_owner SET status = '$status' WHERE owner_id = '$owner_id'";

                if (mysqli_query($conn, $sql_update_status)) {
                    echo "<script>
                            window.location.href = 'index.php';
                            setTimeout(function() {
                                sidebarClick('transaction');
                            }, 500); // Delay to ensure the page has loaded
                          </script>";
                    exit;
                } else {
                    echo "<p>Error updating status in tbl_owner: " . mysqli_error($conn) . "</p>";
                }
            } else {
                echo "<p>Error updating tbl_catch_report: " . mysqli_error($conn) . "</p>";
            }
        } else {
            echo "<p>Catch ID is missing. Please check the form data.</p>";
        }
    }
}

function generateRandomString($length = 8)
{
    $characters = '0123456789';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
