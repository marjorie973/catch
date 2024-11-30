<?php

include '../connection.php';
function generateRandomString($length = 8) {
    $characters = '0123456789';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $catch_id = isset($_POST['catch_id']) ? $_POST['catch_id'] : '';
    $fish_name = isset($_POST['fishname']) ? $_POST['fishname'] : '';

    if ($catch_id && $fish_name) {
        $catched_fish_id = generateRandomString(8);

        $sql_insert_fish = "
            INSERT INTO tbl_catched_fish (catched_fish_id, catch_id, fish_name)
            VALUES ('$catched_fish_id', '$catch_id', '$fish_name')
        ";

        if (mysqli_query($conn, $sql_insert_fish)) {
            echo "<script>
                    window.location.href = 'index.php';
                    setTimeout(function() {
                        sidebarClick('transaction');
                    }, 500); // Delay to ensure the page has loaded
                  </script>";
            exit;
        } else {
            echo "<p>Error: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p>Error: Missing required fields.</p>";
    }
}
?>
