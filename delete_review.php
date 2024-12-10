<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hp";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $review_id = (int)$_GET['id'];
    
    $sql = "DELETE FROM reviews WHERE id = $review_id";
    
    if ($conn->query($sql) === TRUE) {
        echo "Review deleted successfully";
    } else {
        echo "Error deleting review: " . $conn->error;
    }
}

$conn->close();

// Redirect back to the reviews page
header("Location: admin-view-reviews.php");
exit();
?>
