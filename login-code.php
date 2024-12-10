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
