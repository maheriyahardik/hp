<?php
session_start();

// Database connection details
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

// Update order status if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];

    // Update order status
    $sql = "UPDATE orders SET orderStatus = ? WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $order_status, $order_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Order status updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update order status.";
    }

    $stmt->close();

    // Redirect back to the admin order management page
    header("Location: track-order.php");
    exit;
}

// Search functionality
$search_order_id = '';
if (isset($_POST['search_order_id'])) {
    $search_order_id = $_POST['search_order_id'];
}

// Retrieve orders with optional search
$sql = "SELECT o.id as order_id, o.user_id, o.order_date, o.total_price, o.paymentMethod, o.orderStatus, 
        oi.product_id, oi.quantity, p.name, p.image 
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.id LIKE ?
        ORDER BY o.order_date DESC";

$stmt = $con->prepare($sql);
$search_param = '%' . $search_order_id . '%';
$stmt->bind_param("s", $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Order Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .order-table, .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
        }
        .order-table th, .order-table td, .product-table th, .product-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .order-table th, .product-table th {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
        }
        .product-table img {
            max-width: 100px;
            height: auto;
            border-radius: 4px;
        }
        .back-to-home, .back-btn {
            display: inline-block;
            text-align: center;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border: 2px solid #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s;
            margin: 20px auto;
        }
        .back-to-home:hover, .back-btn:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .order-table td select {
            padding: 5px;
            font-size: 14px;
            margin-right: 10px;
        }
        .order-table td button {
            padding: 5px 10px;
            font-size: 14px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .order-table td button:hover {
            background-color: #0056b3;
        }
        .search-form {
            text-align: center;
            margin-bottom: 20px;
        }
        .search-form input[type="text"] {
            padding: 10px;
            font-size: 14px;
            width: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .search-form button {
            padding: 10px 20px;
            font-size: 14px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .search-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Order Management</h2>
        <a href="delivery-orders.php" class="back-btn">Delivery Orders</a>
        <a href="pending-orders.php" class="back-btn">Pending Orders</a>
        <a href="Shipped-order.php" class="back-btn">Shipped Orders</a>
        
        <!-- Search Form -->
        <div class="search-form">
            <form action="track-order.php" method="POST">
                <input type="text" name="search_order_id" placeholder="Search by Order ID" value="<?php echo htmlspecialchars($search_order_id); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

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
                    <h3>Order ID: <?php echo $row['order_id']; ?> (User ID: <?php echo $row['user_id']; ?>)</h3>
                    <table class="order-table">
                        <tr>
                            <th>Order Date</th>
                            <th>Total Price</th>
                            <th>Payment Method</th>
                            <th>Order Status</th>
                            <th>Actions</th>
                        </tr>
                        <tr>
                            <td><?php echo $row['order_date']; ?></td>
                            <td><?php echo $row['total_price']; ?></td>
                            <td><?php echo $row['paymentMethod']; ?></td>
                            <td><?php echo $row['orderStatus']; ?></td>
                            <td>
                                <form action="track-order.php" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                    <select name="order_status">
                                        <option value="Pending" <?php if($row['orderStatus'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                        <option value="Shipped" <?php if($row['orderStatus'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                                        <option value="Delivered" <?php if($row['orderStatus'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                                    </select>
                                    <button type="submit" name="update_status">Update</button>
                                </form>
                            </td>
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
                        <td><img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>"></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                    </tr>
                <?php
            }
            if ($current_order_id !== null) {
                echo '</tbody></table>'; // Close last product-table
            }
            ?>
        <?php } else { ?>
            <p>No orders found.</p>
        <?php } ?>

        <a href="admin-home.php" class="back-to-home">Back to Admin Dashboard</a>
    </div>
</body>
</html>

<?php
$con->close();
?>
