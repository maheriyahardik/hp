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

// Check if user_id is set in the session
$user_id = isset($_SESSION['user']) ? $_SESSION['user']['id'] : 0;


if ($user_id === 0) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit;
}

// Handle remove from wishlist action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_from_wishlist'])) {
    $product_id = $_POST['product_id'];

    // Remove product from wishlist for the specific user
    $sql_remove = "DELETE FROM wishlist WHERE product_id = ? AND user_id = ?";
    $stmt_remove = $con->prepare($sql_remove);
    $stmt_remove->bind_param("ii", $product_id, $user_id);
    $stmt_remove->execute();
    $stmt_remove->close();
}

// Retrieve wishlist products for the specific user
$sql = "SELECT products.* FROM wishlist JOIN products ON wishlist.product_id = products.id WHERE wishlist.user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$con->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
        }
        h2 {
            color: #333;
            font-size: 28px;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        img {
            max-width: 100px;
            height: auto;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        button, a {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            font-size: 16px;
            border-radius: 4px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        button:hover, a:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .remove-btn {
            background-color: #dc3545;
        }
        .remove-btn:hover {
            background-color: #c82333;
        }
        .links {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>My Wishlist</h2>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
                <?php while ($product = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <?php if ($product['image']): ?>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">
                            <?php else: ?>
                                No image available
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="product_details.php?product_id=<?php echo $product['id']; ?>">View</a>
                            <form method="post" action="my_wishlist.php" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" name="remove_from_wishlist" class="remove-btn">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No products in your wishlist.</p>
        <?php endif; ?>
        
        <div class="links">
            <a href="laptop_view.php">Continue Shopping</a>
            <a href="index.php">Back to Home Page</a>
        </div>
    </div>
</body>
</html>
