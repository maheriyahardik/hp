<?php
session_start();
include "connection.php";

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['email']) && isset($_POST['password'])) {
        $email = $_SESSION['email'];
        $password = $_POST['password'];
        
        // Validate password length
        if (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long.';
        } else {
            // Check if the password is unique in the database
            $stmt = $con->prepare("SELECT * FROM users WHERE password = ?");
            $stmt->bind_param("s", $password);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = 'This password is already in use. Please choose a different one.';
            } else {
                // Update the password in the users table
                $stmt = $con->prepare("
                    UPDATE users
                    SET password = ?
                    WHERE email = ?
                ");
                $stmt->bind_param("ss", $password, $email);
                $stmt->execute();
                
                if ($stmt->affected_rows > 0) {
                    $success = 'Password has been updated.';
                    header("Location: index.php");
                    unset($_SESSION['email']); // Clear the email from session
                } else {
                    $error = 'Error updating password.';
                }
            }
        }
    } else {
        $error = 'Invalid request.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 400px;
            width: 100%;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            text-align: left;
        }
        input[type=password] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button[type=submit] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            width: 100%;
            margin-top: 10px;
        }
        button[type=submit]:hover {
            background-color: #45a049;
        }
        .error, .success {
            margin-bottom: 15px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <?php if (!empty($error)) { ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } ?>
        <?php if (!empty($success)) { ?>
            <p class="success"><?php echo $success; ?></p>
        <?php } ?>
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Update Password</button>
        </form>
    </div>
</body>
</html>
