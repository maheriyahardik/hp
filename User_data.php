<?php
session_start();
require 'connection.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['user']['id'];

// Fetch user details from the database
$sql = "SELECT id, username, name, email, mobile_number, password, created_at FROM users WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Close the statement and connection
$stmt->close();
$con->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-header h1 {
            margin: 0;
            color: #333;
        }
        .profile-details {
            list-style: none;
            padding: 0;
        }
        .profile-details li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .profile-details li:last-child {
            border-bottom: none;
        }
        .profile-details span {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .profile-details .password {
            font-style: italic;
            color: #888;
        }
        .change-password {
            text-align: center;
            margin-top: 20px;
        }
        .change-password a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .change-password a:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <h1>User Profile</h1>
        </div>
        <ul class="profile-details">
            <li><span>ID:</span> <?php echo htmlspecialchars($user['id']); ?></li>
            <li><span>Username:</span> <?php echo htmlspecialchars($user['username']); ?></li>
            <li><span>Name:</span> <?php echo htmlspecialchars($user['name']); ?></li>
            <li><span>Email:</span> <?php echo htmlspecialchars($user['email']); ?></li>
            <li><span>Mobile Number:</span> <?php echo htmlspecialchars($user['mobile_number']); ?></li>
            <li><span>Password:</span> <?php echo htmlspecialchars($user['password']); ?></li>
            <li><span>Created At:</span> <?php echo htmlspecialchars($user['created_at']); ?></li>
        </ul>
    </div>
</body>
</html>
