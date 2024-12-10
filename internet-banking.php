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
// Check if user is logged in
if (!isset($_SESSION['user']['id'])) {
    echo "You must be logged in to view this page.";
    exit;
}



// Initialize total price
$total_price = 0;

// Retrieve total price from the orders table for the current user
$sql = "SELECT total_price FROM orders WHERE user_id = ? AND orderStatus = 'Pending' ORDER BY order_date DESC LIMIT 1";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $total_price = $row['total_price'];
}
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $bank_name = $_POST['bank_name'] ?? '';
    $account_number = $_POST['account_number'] ?? '';
    $ifsc_code = $_POST['ifsc_code'] ?? '';
    $transaction_amount = $_POST['transaction_amount'] ?? '';
    $payment_method = 'internet-banking';

    // Validate required fields
    if (empty($bank_name) || empty($account_number) || empty($ifsc_code)) {
        echo "Please fill in all required fields.";
        exit;
    }

    // Place the order
    placeOrder($payment_method, $transaction_amount);
}

// Function to place the order in the database
function placeOrder($payment_method, $transaction_amount) {
    global $con, $user_id;

    // Retrieve products in cart
    $sql = "SELECT p.id, p.price, c.quantity FROM products p JOIN carts c ON p.id = c.product_id WHERE c.user_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize total price and prepare data for order insertion
    $total_price = 0;
    $order_details = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $total_price += $row['price'] * $row['quantity'];
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
    <title>Internet Banking Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .payment-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .payment-container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .image-link {
            text-align: center;
            margin-bottom: 20px;
        }
        .image-link img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .total-price {
            text-align: center;
            margin: 20px 0;
        }
        .total-price h3 {
            font-size: 20px;
            color: #333;
            margin: 0;
        }
        .bank-details {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }
        input[type="text"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
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
    </style>
</head>
<body>
    <div class="payment-container">
        <h2>Pay with Internet Banking</h2>

        <!-- Image Link Section -->
        <div class="image-link">
            <a href="link_url_here" target="_blank">
                <img src="images/qr.jpg" alt="QR Code for Payment">
            </a>
        </div>

        <!-- Display Total Price -->
        <div class="total-price">
            <h3>Total Price: ₹<?php echo number_format($total_price, 2); ?></h3>
        </div>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="bank-details">
                <label for="bank_name">Bank Name:</label>
                <input type="text" id="bank_name" name="bank_name" placeholder="Enter Bank Name" required>

                <label for="account_number">Account Number:</label>
                <input type="text" id="account_number" name="account_number" placeholder="Enter Account Number" required>

                <label for="ifsc_code">IFSC Code:</label>
                <input type="text" id="ifsc_code" name="ifsc_code" placeholder="Enter IFSC Code" required>
            </div>

            <input type="hidden" name="transaction_amount" value="<?php echo $total_price; ?>">
            <input type="submit" value="Submit Payment">
        </form>

        <a href="index.php" class="back-to-home">Back to Home</a>
    </div>
</body>
</html>
