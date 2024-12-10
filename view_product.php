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

// Get the product ID from the URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Retrieve product details
$sql = "SELECT p.id, c.name as category, s.name as subcategory, p.name as product_name, p.price, p.description as product_description, p.image 
        FROM products p
        JOIN subcategories s ON p.subcategory_id = s.id
        JOIN categories c ON s.category_id = c.id
        WHERE p.id = $product_id";
$result = $conn->query($sql);

// Check if the product exists
if ($result && $result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    die("Product not found.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 960px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #00796b;
        }

        .product-details {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .product-details img {
            max-width: 100%;
            max-height: 300px;
            margin-bottom: 20px;
            object-fit: contain;
        }

        .product-details h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #004d40;
        }

        .product-details p {
            margin: 5px 0;
            color: #00796b;
        }

        .back-to-list {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #00796b;
            border: 1px solid #ccc;
            padding: 8px 12px;
            border-radius: 4px;
            width: fit-content;
            margin: 20px auto;
        }

        .back-to-list:hover {
            background-color: #e0f7fa;
        }
    </style>
</head>
<body>

<div class="container">
    <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>

    <div class="product-details">
        <?php if (isset($product["image"])) { ?>
            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">
        <?php } else { ?>
            <p>No image available</p>
        <?php } ?>
        <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
        <p><strong>Subcategory:</strong> <?php echo htmlspecialchars($product['subcategory']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($product['product_description']); ?></p>
        <p><strong>Price:</strong> $<?php echo htmlspecialchars($product['price']); ?></p>
    </div>
</div>

<a href="index.php" class="back-to-list">Back to Product List</a>

</body>
</html>

<?php
$conn->close();
?>
