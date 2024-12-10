<?php
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST['category'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $product_description = $_POST['product_description'];

    // File upload handling (product images)
    $target_dir = "uploads/";
    $product_image1 = $target_dir . basename($_FILES["product_image1"]["name"]);

    // Check if the uploads directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Move uploaded files to the specified directory
    if (move_uploaded_file($_FILES["product_image1"]["tmp_name"], $product_image1)) {
        $sql = "INSERT INTO products (category, product_name, price, product_description, product_image1) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssdss', $category, $product_name, $price, $product_description, $product_image1);

        if ($stmt->execute()) {
            echo "New product inserted successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Error uploading file.";
    }
}

$conn->close();
?>
