<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $phonenumber = $_POST['phonenumber'];
    $address = $_POST['address'];
    $username = $_POST['username'];
    $password = $_POST['password']; 
    $confirmpassword = $_POST['confirmpassword'];

    if ($password !== $confirmpassword) {
        $alertMessage = "Passwords do not match!";
    } else {
        $broker_id = bin2hex(random_bytes(8)); 

        $sql = "INSERT INTO tbl_owner (owner_id, owner_lname, owner_fname, owner_mname, phonenum, address, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        
        mysqli_stmt_bind_param($stmt, "ssssssss", $broker_id, $lastname, $firstname, $middlename, $phonenumber, $address, $username, $password);
        
        if (mysqli_stmt_execute($stmt)) {
            $alertMessage = "Account created successfully!";
            header("Location: login.php");
        } else {
            $alertMessage = "Error: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Signup</title>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel='stylesheet' href='index.css'>
    <script>
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
                        <h2>OWNER ACCOUNT</h2>
                        <div class="inputbox"> <ion-icon name="person-outline"></ion-icon> <input type="text" name="firstname" required>
                            <label>First Name</label>
                        </div>
                        <div class="inputbox"> <ion-icon name="person-outline"></ion-icon> <input type="text" name="middlename" required>
                            <label>Middle Name</label>
                        </div>
                        <div class="inputbox"> <ion-icon name="person-outline"></ion-icon> <input type="text" name="lastname" required>
                            <label>Last Name</label>
                        </div>
                        <div class="inputbox"> <ion-icon name="call-outline"></ion-icon> <input type="tel" name="phonenumber" required>
                            <label>Phone Number</label>
                        </div>
                        <div class="inputbox"> <ion-icon name="location-outline"></ion-icon> <input type="text" name="address" required>
                            <label>Address</label>
                        </div>

                        <div class="inputbox"> <ion-icon name="person-outline"></ion-icon> <input type="text" name="username" required>
                            <label>Username</label>
                        </div>
                        <div class="inputbox"> <ion-icon name="lock-closed-outline"></ion-icon> <input type="password" name="password" required> 
                            <label>Password</label> 
                        </div>
                        <div class="inputbox"> <ion-icon name="lock-closed-outline"></ion-icon> <input type="password" name="confirmpassword" required> 
                            <label>Confirm Password</label> 
                        </div>
                        <button type="submit">Create Account</button>
                        <div class="register">
                            <p>Already have an account? <a href="login.php">Login</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </body>
</body>
</html>