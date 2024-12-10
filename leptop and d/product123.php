<!DOCTYPE html>
<html>
<head>
    <title>Product Listing</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <style>
        /* styles.css */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .product {
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
        }
        .product img {
            max-width: 100px;
            margin-right: 20px;
            vertical-align: middle;
        }
        .product-name {
            font-size: 1.2em;
            font-weight: bold;
        }
        .product-description {
            margin-top: 10px;
        }
    </style>

    <div class="container">
        <h2>Product Listing</h2>

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

        $sql = "SELECT category, product_name, price, product_description, product_image1 FROM products";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="product">';
                if (!empty($row["product_image1"])) {
                    echo '<img src="' . $row["product_image1"] . '" alt="' . $row["product_name"] . '">';
                }
                echo '<div class="product-name">' . $row["product_name"] . '</div>';
                echo '<div class="product-category"><strong>Category:</strong> ' . $row["category"] . '</div>';
                echo '<div class="product-price"><strong>Price:</strong> $' . $row["price"] . '</div>';
                echo '<div class="product-description">' . $row["product_description"] . '</div>';
                echo '</div>';
            }
        } else {
            echo "No products found.";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
