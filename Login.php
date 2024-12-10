<?php
session_start();
include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prepare and bind statements to prevent SQL injection
        $stmtAdmin = $con->prepare("SELECT * FROM admins WHERE email = ? AND password = ?");
        $stmtAdmin->bind_param("ss", $email, $password);
        $stmtAdmin->execute();
        $resultAdmin = $stmtAdmin->get_result();

        $stmtUser = $con->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
        $stmtUser->bind_param("ss", $email, $password);
        $stmtUser->execute();
        $resultUser = $stmtUser->get_result();

        // Check for admin
        if ($rowAdmin = $resultAdmin->fetch_assoc()) {
            $_SESSION["admin"] = $rowAdmin;
            header("Location: admin-home.php");
            exit();
        } 
        // Check for user
        else if ($rowUser = $resultUser->fetch_assoc()) {
            $_SESSION["user"] = $rowUser;
            header("Location: index.php");
            exit();
        } 
        // Invalid credentials
        else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Email and password are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="main-layout inner_posituong computer_page">
    <style>
        .login-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        .login-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            border: none;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
        .login-container a {
            display: block;
            margin-top: 10px;
            text-align: center;
            color: #007BFF;
            text-decoration: none;
        }
        button[type=submit], .back-btn, .styled-link {
            background-color: #4CAF50;
            color: white;
            padding: 13px 1px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            width: 90%;
            margin-top: 10px;
            text-align: center;
            text-decoration: none;
        }
        button[type=submit]:hover, .back-btn:hover, .styled-link:hover {
            background-color: #45a049;
        }
        .forgot-password {
            text-align: center;
            margin-top: 10px;
        }
    </style>
    <!-- login form -->
    <div class="login-container">
        <h2>User Login</h2>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form action="Login.php" method="POST">
            <div class="form-group" style="width:95%;">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group" style="width:95%;">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit"  style="width:100%;" >Login</button>
        </form>
       
        <a href="register.php" class="styled-link" style="color:white;width:100%;" >Register</a>
        <a href="index.php" class="back-btn"  style="color:white;width:100%;"  >Back to Home</a>
        <div class="forgot-password">
            <a href="forgot_password.php"  >Forgot Password?</a>
        </div>
    </div>
    <!-- Javascript files-->
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery-3.0.0.min.js"></script>
    <script src="js/plugin.js"></script>
    <!-- sidebar -->
    <script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="js/custom.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>
</body>
</html>
