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

// Ensure user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit;
}

// Retrieve user ID from session
$user_id = intval($_SESSION['user']['id']);

// Retrieve orders for the logged-in user
$sql = "SELECT o.id as order_id, o.order_date, o.total_price, o.paymentMethod, o.orderStatus, 
        oi.product_id, oi.quantity, p.name, p.image 
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = ?";  // Corrected condition

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .container h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .order-table th, .order-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .order-table th {
            background-color: #f2f2f2;
        }
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .product-table th, .product-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .product-table th {
            background-color: #f2f2f2;
        }
        .product-table img {
            max-width: 100px;
            height: auto;
            border-radius: 4px;
        }
        .back-to-home {
            display: block;
            text-align: center;
            text-decoration: none;
            color: #007bff;
            border: 2px solid #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #fff;
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s;
            margin: 20px auto;
            width: max-content;
        }
        .back-to-home:hover {
            background-color: #007bff;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>My Orders</h2>

        <?php if ($result->num_rows > 0) { ?>
            <?php
            $current_order_id = null;
            while ($row = $result->fetch_assoc()) {
                if ($current_order_id != $row['order_id']) {
                    if ($current_order_id !== null) {
                        echo '</tbody></table>'; // Close previous product-table
                    }
                    $current_order_id = $row['order_id'];
                    ?>
                    <h3>Order ID: <?php echo htmlspecialchars($row['order_id']); ?></h3>
                    <table class="order-table">
                        <tr>
                            <th>Order Date</th>
                            <th>Total Price</th>
                            <th>Payment Method</th>
                            <th>Order Status</th>
                        </tr>
                        <tr>
                            <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($row['total_price'], 2)); ?></td>
                            <td><?php echo htmlspecialchars($row['paymentMethod']); ?></td>
                            <td><?php echo htmlspecialchars($row['orderStatus']); ?></td>
                        </tr>
                    </table>
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>Product Image</th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                    <?php
                }
                ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>"></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    </tr>
                <?php
            }
            if ($current_order_id !== null) {
                echo '</tbody></table>'; // Close last product-table
            }
            ?>
        <?php } else { ?>
            <p>You have no orders.</p>
        <?php } ?>

        <a href="index.php" class="back-to-home">Back to Home</a>
    </div>
</body>
</html>

<?php
$stmt->close();
$con->close();
?>
