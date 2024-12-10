<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobile_number = trim($_POST['mno']);
    $password = $_POST['password'];

    // Server-side validation
    if (!preg_match("/^[a-zA-Z0-9_]{5,20}$/", $username)) {
        echo "Error: Invalid username format. Username should be 5-20 characters long and contain only letters, numbers, and underscores.";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Error: Invalid email format.";
        exit();
    }

    if (!preg_match("/^[0-9]{10}$/", $mobile_number)) {
        echo "Error: Invalid mobile number format. Please enter a 10-digit number.";
        exit();
    }

    if (strlen($password) < 8) {
        echo "Error: Password should be at least 8 characters long.";
        exit();
    }

    // Check for duplicate username
    $stmt = $con->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Error: Username already exists. Please choose a different one.";
    } else {
        // Check for duplicate email
        $stmt = $con->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "Error: Email already exists. Please choose a different one.";
        } else {
            // Check for duplicate mobile number
            $stmt = $con->prepare("SELECT * FROM users WHERE mobile_number = ?");
            $stmt->bind_param("s", $mobile_number);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "Error: Mobile number already exists. Please choose a different one.";
            } else {
                // Prepare and bind
                $stmt = $con->prepare("INSERT INTO users (username, name, email, mobile_number, password) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $username, $name, $email, $mobile_number, $password);

                // Execute the statement
                if ($stmt->execute()) {
                    header("Location: index.php");
                    exit();
                } else {
                    echo "Error: " . $stmt->error;
                }
            }
        }
    }

    // Close the statement and connection
    $stmt->close();
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function validateForm() {
            const username = document.getElementById('username').value;
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const mobileNumber = document.getElementById('mno').value;
            const password = document.getElementById('password').value;

            const usernameRegex = /^[a-zA-Z0-9_]{5,20}$/;
            const emailRegex = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/;
            const mobileNumberRegex = /^[0-9]{10}$/;

            if (!usernameRegex.test(username)) {
                alert("Invalid username format. Username should be 5-20 characters long and contain only letters, numbers, and underscores.");
                return false;
            }

            if (!emailRegex.test(email)) {
                alert("Invalid email format.");
                return false;
            }

            if (!mobileNumberRegex.test(mobileNumber)) {
                alert("Invalid mobile number format. Please enter a 10-digit number.");
                return false;
            }

            if (password.length < 8) {
                alert("Password should be at least 8 characters long.");
                return false;
            }

            return true;
        }
    </script>
</head>
<body class="main-layout inner_positioning computer_page">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .login-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .login-container h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }

        .login-container .form-group {
            margin-bottom: 15px;
        }

        .login-container .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        .login-container .form-group input {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            border: none;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .login-container button:hover {
            background-color: #0056b3;
        }

        .login-container a {
            display: block;
            margin-top: 10px;
            text-align: center;
            color: #007BFF;
        }

        .login-container a:hover {
            color: #0056b3;
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
            color: red;
            font-size: 16px;
        }
    </style>
    <!-- registration form -->
    <div class="login-container">
        <h2>User Registration</h2>
        <form action="" method="post" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="mno">Mobile Number:</label>
                <input type="text" id="mno" name="mno" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Register</button>
            <a href="index.php" class="back-btn">Back to Home Page</a>
        </form>
        <a href="login.php">Login</a>
    </div>
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
