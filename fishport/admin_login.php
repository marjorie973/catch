<?php
include 'connection.php';

$alertMessage = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['usrname'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM tbl_broker WHERE username = ? AND password = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        $_SESSION["userlogin"] = "admin";
        $_SESSION["id"] = $row['broker_id'];

        $alertMessage = "Login successful!";
        header("Location: admin/index.php");
        exit();
    } else {
        $alertMessage = "Invalid username or password."; 
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Login</title>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel='stylesheet' href='index.css'>
    <script>
        // Display alert if there is a message
        window.onload = function() {
            <?php if ($alertMessage): ?>
                alert("<?php echo $alertMessage; ?>");
            <?php endif; ?>
        };
    </script>
</head>

<body>

    <body>
        <section>
            <div class="form-box">
                <div class="form-value">
                    <form method="POST" action="">
                        <h2>Login Admin</h2>
                        <div class="inputbox"> <ion-icon name="person-outline"></ion-icon> <input type="text" name="usrname" required>
                            <label>Username</label>
                        </div>
                        <div class="inputbox"> <ion-icon name="lock-closed-outline"></ion-icon> <input type="password" name="password" required> 
                            <label>Password</label> 
                        </div>
                        <button type="submit">Log In</button>
                        <div class="register">
                            <p>Login as <a href="login.php">User</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </section> 
    </body>
</body>

</html>