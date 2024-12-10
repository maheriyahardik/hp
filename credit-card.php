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

// Check if user is logged in
if (!isset($_SESSION['user']['id'])) {
    echo "You must be logged in to view this page.";
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION['user']['id'];

// Retrieve total amount from cart
function getCartTotal($user_id, $con) {
    $sql = "SELECT SUM(p.price * c.quantity) as total_price FROM products p JOIN carts c ON p.id = c.product_id WHERE c.user_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_price = 0;

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_price = $row['total_price'];
    }

    $stmt->close();
    return $total_price;
}

// Initialize total price
$total_price = getCartTotal($user_id, $con);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $card_number = htmlspecialchars($_POST['card_number'] ?? '');
    $expiry_date = htmlspecialchars($_POST['expiry_date'] ?? '');
    $cvv = htmlspecialchars($_POST['cvv'] ?? '');
    $payment_method = 'Credit Card';

    // Validate required fields
    if (empty($card_number) || empty($expiry_date) || empty($cvv)) {
        echo "Please fill in all required fields.";
        exit;
    }

    // Place the order
    if ($total_price > 0) {
        placeOrder($payment_method, $total_price, $user_id, $con);
    } else {
        echo "Your cart is empty.";
    }
}

// Function to place the order in the database
function placeOrder($payment_method, $total_price, $user_id, $con) {
    // Retrieve products in cart
    $sql = "SELECT p.id, p.price, c.quantity FROM products p JOIN carts c ON p.id = c.product_id WHERE c.user_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize order details for insertion
    $order_details = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $order_details[] = [
                'product_id' => $row['id'],
                'price' => $row['price'],
                'quantity' => $row['quantity']
            ];
        }
    }

    // Insert order into orders table
    if (!empty($order_details)) {
        $order_date = date('Y-m-d H:i:s');
        $order_status = 'Pending'; // Example: Initial order status
        $insert_order_sql = "INSERT INTO orders (order_date, total_price, paymentMethod, orderStatus, user_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $con->prepare($insert_order_sql);
        $stmt->bind_param("sissi", $order_date, $total_price, $payment_method, $order_status, $user_id);

        if ($stmt->execute()) {
            $order_id = $stmt->insert_id;
            $stmt->close();

            // Insert order details into order_items table
            $insert_order_items_sql = "INSERT INTO order_items (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)";
            $stmt_items = $con->prepare($insert_order_items_sql);

            foreach ($order_details as $item) {
                $stmt_items->bind_param("iidi", $order_id, $item['product_id'], $item['price'], $item['quantity']);
                $stmt_items->execute();
            }

            $stmt_items->close();

            // Clear cart after placing order
            $clear_cart_sql = "DELETE FROM carts WHERE user_id = ?";
            $stmt_clear_cart = $con->prepare($clear_cart_sql);
            $stmt_clear_cart->bind_param("i", $user_id);
            if ($stmt_clear_cart->execute()) {
                echo "Order placed successfully. Your order ID is: " . $order_id;
            } else {
                echo "Error clearing cart: " . $con->error;
            }
            $stmt_clear_cart->close();
        } else {
            echo "Error placing order: " . $stmt->error;
        }
    } else {
        echo "No items in the cart.";
    }
}

$con->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit Card Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            background-color: #f9f9f9;
        }
        .payment-container {
            max-width: 600px;
            margin: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .payment-container h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        .card-details {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }
        input[type="text"], input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .back-to-home {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #007bff;
            text-decoration: none;
        }
        .back-to-home:hover {
            text-decoration: underline;
        }
        .total-price-box {
            background-color: #f0f8ff;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h2>Pay with Credit Card</h2>
        
        <!-- Display the total price -->
        <?php if ($total_price > 0) : ?>
            <div class="total-price-box">
                <h3>Total Price: $<?php echo number_format($total_price, 2); ?></h3>
            </div>
        <?php else: ?>
            <div class="total-price-box">
                <h3>Your cart is empty.</h3>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="card-details">
                <label for="card_number">Card Number:</label>
                <input type="text" id="card_number" name="card_number" placeholder="Enter Card Number" required>

                <label for="expiry_date">Expiry Date:</label>
                <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" required>

                <label for="cvv">CVV:</label>
                <input type="password" id="cvv" name="cvv" placeholder="Enter CVV" required>
            </div>

            <input type="submit" value="Confirm Payment">
            <a href="laptop_view.php" class="back-to-home">Back to Home</a>
        </form>
    </div>
</body>
</html>
