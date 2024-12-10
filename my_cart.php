<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hp";
$con = new mysqli($servername, $username, $password, $dbname);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Assuming user_id is stored in session
$user_id = intval($_SESSION['user']['id']);

if ($user_id === 0) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit;
}

// Handle update quantity
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            // Validate and sanitize input if needed
            $quantity = intval($quantity); // Convert to integer for safety
            $product_id = intval($product_id);

            // Update quantity in cart table
            $update_sql = "UPDATE carts SET quantity = $quantity WHERE product_id = $product_id AND user_id = $user_id";
            $con->query($update_sql);
        }
    } elseif (isset($_POST['remove_product'])) {
        $product_id = intval($_POST['product_id']);
        // Remove the product from the cart
        $remove_sql = "DELETE FROM carts WHERE product_id = $product_id AND user_id = $user_id";
        $con->query($remove_sql);
    } 
}

// Retrieve products in cart for the logged-in user
$sql = "SELECT p.id, p.name, p.price, c.quantity FROM products p JOIN carts c ON p.id = c.product_id WHERE c.user_id = $user_id";
$result = $con->query($sql);

// Check if user has an address
$address_check_sql = "SELECT COUNT(*) AS address_count FROM addres WHERE user_id = $user_id";
$address_check_result = $con->query($address_check_sql);
$address_count = $address_check_result->fetch_assoc()['address_count'];

// Initialize total price
$total_price = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include "menu1.php";?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
    
        background-color: #f9f9f9;
    }
    .cart-container {
        max-width: 900px;
        margin: auto;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #fff;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .cart-container h2 {
        font-size: 28px;
        margin-bottom: 15px;
        color: #333;
    }
    .cart-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .cart-table th, .cart-table td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }
    .cart-table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }
    .cart-table td {
        background-color: #fafafa;
    }
    .cart-table input[type="number"] {
        width: 60px;
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .cart-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .cart-table tr:hover {
        background-color: #f1f1f1;
    }
    .cart-total {
        margin-top: 20px;
        font-size: 20px;
        font-weight: bold;
        border-top: 2px solid #ddd;
        padding-top: 10px;
    }
    .cart-actions {
        margin-top: 20px;
        text-align: right;
    }
    .cart-actions button, .cart-actions a {
        display: inline-block;
        margin-left: 10px;
        text-decoration: none;
        padding: 12px 24px;
        border-radius: 5px;
        color: #fff;
        background-color: #007bff;
        transition: background-color 0.3s ease, transform 0.2s ease;
        border: none;
        cursor: pointer;
        font-size: 16px;
    }
    .cart-actions button:hover, .cart-actions a:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }
    .back-to-home {
        display: inline-block;
        margin-top: 20px;
        text-decoration: none;
        padding: 12px 24px;
        background-color: #555;
        color: #fff;
        border-radius: 5px;
        transition: background-color 0.3s ease, transform 0.2s ease;
        font-size: 16px;
    }
    .back-to-home:hover {
        background-color: #333;
        transform: scale(1.05);
    }
    .btn-contact-shipping {
        display: inline-block;
        text-decoration: none;
        padding: 12px 24px;
        background-color: #28a745;
        color: #fff;
        border-radius: 5px;
        transition: background-color 0.3s ease, transform 0.2s ease;
        font-size: 16px;
    }
    .btn-contact-shipping:hover {
        background-color: #218838;
        transform: scale(1.05);
    }
    .warning-message {
        margin-top: 20px;
        padding: 15px;
        background-color: #ffeb3b;
        color: #333;
        border: 1px solid #fdd835;
        border-radius: 5px;
        font-size: 16px;
    }
    </style>
</head>
<body>
    <div class="cart-container mt-3">
        <h2>My Cart</h2>

        <?php if ($result->num_rows > 0): ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <table class="cart-table">
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                            $product_id = $row['id'];
                            $product_name = $row['name'];
                            $price = $row['price'];
                            $quantity = $row['quantity'];
                            $total_per_item = $price * $quantity;
                            $total_price += $total_per_item;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product_name); ?></td>
                            <td><?php echo number_format($price, 2); ?></td>
                            <td><input type="number" name="quantity[<?php echo $product_id; ?>]" value="<?php echo $quantity; ?>" min="1" max="100"></td>
                            <td><?php echo number_format($total_per_item, 2); ?></td>
                            <td>
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                    <button type="submit" name="remove_product">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <tr class="cart-total">
                        <td colspan="4" align="right"><strong>Total:</strong></td>
                        <td><?php echo number_format($total_price, 2); ?></td>
                    </tr>
                </table>

                <div class="cart-actions">
                    <?php if ($address_count == 0): ?>
                        <div class="warning-message">
                            <p>You have not added an address yet. Please add an address to proceed with checkout.</p>
                        </div>
                    <?php endif; ?>
                    <button type="submit" name="update_cart">Update Cart</button>
                    <a href="checkout.php">Add Address</a>
                    <a href="payment-method.php">Payment</a>
                </div>
            </form>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
        <a href="index.php" class="back-to-home">Back to Home</a>
        <a href="laptop_view.php" class="btn-contact-shipping">Contact Shipping</a>
        <a href="view-addresses.php" class="btn-contact-shipping">View Addresses</a>
    </div>
</body>
</html>

<?php
$con->close();
?>
