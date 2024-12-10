<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hp";

// Create connection
$con = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to perform this action.");
}

$user_id = $_SESSION['user_id'];

// Function to clear the cart for a specific user
function clearCart($con, $user_id) {
    $sql = "DELETE FROM carts WHERE user_id = ?";
    $stmt = $con->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $con->error);
    }
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute() === false) {
        die("Execute failed: " . $stmt->error);
    }
    $stmt->close();
}

// Clear cart for the logged-in user
clearCart($con, $user_id);
echo "Cart cleared successfully.";

$con->close();
?>
