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

// Assuming the user is logged in and user_id is stored in the session
$user_id = $_SESSION['user_id'];

// Retrieve products in cart for the logged-in user
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
            'product_name' => $product_name,
            'price' => $price,
            'quantity' => $quantity,
            'total_per_item' => $total_per_item
        ];
    }
}

// Insert order into orders table
if (!empty($order_details)) {
    $order_date = date('Y-m-d H:i:s');
    $insert_order_sql = "INSERT INTO orders (user_id, order_date, total_price) VALUES (?, ?, ?)";
    $stmt = $con->prepare($insert_order_sql);
    $stmt->bind_param("isd", $user_id, $order_date, $total_price);
    
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            background-color: #f9f9f9;
        }
        .order-details {
            max-width: 800px;
            margin: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .order-details h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .order-table th, .order-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .order-table th {
            background-color: #f2f2f2;
        }
        .order-total {
            margin-top: 20px;
            font-size: 18px;
        }
        .order-actions {
            margin-top: 20px;
        }
        .order-actions a {
            display: inline-block;
            margin-right: 10px;
            text-decoration: none;
            padding: 8px 16px;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .order-actions a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="order-details">
        <h2>Order Details</h2>

        <?php if (!empty($order_details)): ?>
            <table class="order-table">
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
                <?php foreach ($order_details as $item): ?>
                    <tr>
                        <td><?php echo $item['product_name']; ?></td>
                        <td>$<?php echo $item['price']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['total_per_item'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="order-total">
                    <td colspan="3" align="right"><strong>Total:</strong></td>
                    <td>$<?php echo number_format($total_price, 2); ?></td>
                </tr>   
            </table>

            <div class="order-actions">
                <a href="index.php">Continue Shopping</a>
            </div>
        <?php else: ?>
            <p>No items in the order.</p>
        <?php endif; ?>
    </div>
</body>
</html>
