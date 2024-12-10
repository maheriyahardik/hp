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

// Handle placing the order
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    // Ensure user is logged in
    if (!isset($_SESSION['user']['id'])) {
        echo "You must be logged in to place an order.";
        exit();
    }

    // Process checkout form data
    $shipping_address = $_POST['shipping_address'];
    $billing_address = $_POST['billing_address'];
    $billing_state = $_POST['billing_state'];
    $billing_city = $_POST['billing_city'];
    $billing_pincode = $_POST['billing_pincode'];
    $shipping_state = $_POST['shipping_state'];
    $shipping_city = $_POST['shipping_city'];
    $shipping_pincode = $_POST['shipping_pincode'];
    $user_id = $_SESSION['user']['id']; // Ensure user is logged in and user_id is available

    // Insert data into database
    $insert_sql = "INSERT INTO addres (user_id, shipping_address, billing_address, billing_state, billing_city, billing_pincode, shipping_state, shipping_city, shipping_pincode) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($insert_sql);
    $stmt->bind_param("issssssss", $user_id, $shipping_address, $billing_address, $billing_state, $billing_city, $billing_pincode, $shipping_state, $shipping_city, $shipping_pincode);

    if ($stmt->execute()) {
        // Redirect to payment-method.php after successful order placement
        header("Location: payment-method.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Retrieve products in cart for the current user
$user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
$sql = "SELECT p.id, p.name, p.price, c.quantity FROM products p JOIN carts c ON p.id = c.product_id WHERE c.user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Initialize total price
$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            background-color: #f9f9f9;
        }
        .checkout-container {
            max-width: 800px;
            margin: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .checkout-container h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }
        .checkout-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .checkout-table th, .checkout-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .checkout-table th {
            background-color: #f2f2f2;
        }
        .checkout-total {
            margin-top: 20px;
            font-size: 18px;
        }
        .checkout-actions {
            margin-top: 20px;
        }
        .checkout-actions a {
            display: inline-block;
            margin-right: 10px;
            text-decoration: none;
            padding: 8px 16px;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .checkout-actions a:hover {
            background-color: #0056b3;
        }
        .checkout-form {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="number"] {
            width: calc(100% - 12px);
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group textarea {
            width: calc(100% - 12px);
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group select {
            width: calc(100% - 12px);
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group input[type="submit"] {
            padding: 10px 20px;
            font-size: 18px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .form-group input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <h2>Checkout</h2>

        <?php if ($result->num_rows > 0): ?>
            <table class="checkout-table">
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                        $product_name = $row['name'];
                        $price = $row['price'];
                        $quantity = $row['quantity'];
                        $total_per_item = $price * $quantity;
                        $total_price += $total_per_item;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product_name); ?></td>
                        <td>$<?php echo htmlspecialchars($price); ?></td>
                        <td><?php echo htmlspecialchars($quantity); ?></td>
                        <td>$<?php echo number_format($total_per_item, 2); ?></td>
                    </tr>
                <?php endwhile; ?>
                <tr class="checkout-total">
                    <td colspan="3" align="right"><strong>Total:</strong></td>
                    <td>$<?php echo number_format($total_price, 2); ?></td>
                </tr>
            </table>

            <div class="checkout-actions">
                <a href="?clear_cart=1">Clear Cart</a>
            </div>

            <div class="checkout-form">
                <h2>Shipping Information</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="form-group">
                        <label for="shipping_address">Shipping Address</label>
                        <textarea id="shipping_address" name="shipping_address" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="billing_address">Billing Address</label>
                        <textarea id="billing_address" name="billing_address" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="billing_state">Billing State</label>
                        <input type="text" id="billing_state" name="billing_state" required>
                    </div>
                    <div class="form-group">
                        <label for="billing_city">Billing City</label>
                        <input type="text" id="billing_city" name="billing_city" required>
                    </div>
                    <div class="form-group">
                        <label for="billing_pincode">Billing Pincode</label>
                        <input type="number" id="billing_pincode" name="billing_pincode" required>
                    </div>
                    <div class="form-group">
                        <label for="shipping_state">Shipping State</label>
                        <input type="text" id="shipping_state" name="shipping_state" required>
                    </div>
                    <div class="form-group">
                        <label for="shipping_city">Shipping City</label>
                        <input type="text" id="shipping_city" name="shipping_city" required>
                    </div>
                    <div class="form-group">
                        <label for="shipping_pincode">Shipping Pincode</label>
                        <input type="number" id="shipping_pincode" name="shipping_pincode" required>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="place_order" value="Place Order">
                    </div>
                </form>
            </div>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Clear cart after placing the order
if (isset($_GET['clear_cart'])) {
    $clear_cart_sql = "DELETE FROM carts WHERE user_id = ?";
    $stmt = $con->prepare($clear_cart_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    // Redirect to checkout page to refresh the cart
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Close the database connection
$con->close();
?>
