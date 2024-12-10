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
$user_id = $_SESSION['user']['id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize the selected payment method
    if (isset($_POST['payment_method'])) {
        $payment_method = $_POST['payment_method'];
        
        // Process the selected payment method
        switch ($payment_method) {
            case 'COD':
                // Directly place the order as Cash on Delivery
                placeOrder('Cash on Delivery', $user_id);
                break;
            case 'Internet Banking':
                // Redirect to Internet Banking gateway
                header("Location: internet-banking.php");
                exit;
            case 'Debit / Credit card':
                // Redirect to Credit Card payment gateway
                header("Location: credit-card.php");
                exit;
            default:
                $message = "Invalid payment method selected.";
                $message_type = "error";
        }
    } else {
        $message = "Please select a payment method.";
        $message_type = "error";
    }
}

// Function to place the order in the database
function placeOrder($payment_method, $user_id) {
    global $con;
    global $message;
    global $message_type;

    // Retrieve products in cart
    $sql = "SELECT p.id, p.name, p.price, c.quantity FROM products p JOIN carts c ON p.id = c.product_id WHERE c.user_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize total price and prepare data for order insertion
    $total_price = 0;
    $order_details = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $product_id = $row['id'];
            $product_name = $row['name'];
            $price = $row['price'];
            $quantity = $row['quantity'];
            $total_per_item = $price * $quantity;

            // Calculate total price
            $total_price += $total_per_item;

            // Collect order details for insertion
            $order_details[] = [
                'product_id' => $product_id,
                'price' => $price,
                'quantity' => $quantity,
                'total_per_item' => $total_per_item
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
                $message = "Order placed successfully. Your order ID is: " . $order_id;
                $message_type = "success";
            } else {
                $message = "Error clearing cart: " . $con->error;
                $message_type = "error";
            }
            $stmt_clear_cart->close();
        } else {
            $message = "Error placing order: " . $stmt->error;
            $message_type = "error";
        }
    } else {
        $message = "No items in the cart.";
        $message_type = "error";
    }
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Payment Method</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            background-color: #f9f9f9;
        }
        .payment-method {
            max-width: 400px;
            margin: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .payment-method h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
            text-align: center;
        }
        .payment-options {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .payment-options label {
            display: block;
            cursor: pointer;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .payment-options label:hover {
            background-color: #f2f2f2;
        }
        .payment-options input[type="submit"] {
            margin-top: 20px;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .payment-options input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="payment-method">
        <h2>Choose Payment Method</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="payment-options">
                <label>
                    <input type="radio" name="payment_method" value="COD" required>
                    Cash on Delivery
                </label>
                <label>
                    <input type="radio" name="payment_method" value="Internet Banking" required>
                    Internet Banking
                </label>
                <label>
                    <input type="radio" name="payment_method" value="Debit / Credit card" required>
                    Debit / Credit card
                </label>
            </div>

            <input type="submit" value="Proceed to Payment">
        </form>
        <?php if (isset($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
