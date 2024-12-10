<?php
// Include database connection
include('connection.php');

// Start session if needed
session_start();

// Check if the user is an admin
// Add your admin check logic here

// Fetch delivery orders from the database
$sql = "SELECT orders.id AS order_id, orders.user_id, orders.order_date, orders.orderStatus, 
               order_items.product_id, order_items.quantity 
        FROM orders 
        JOIN order_items ON orders.id = order_items.order_id 
        WHERE orders.orderStatus = 'Delivered'"; // Adjust the condition based on your database structure

$result = $con->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Orders</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            color: #212529;
        }
        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
            font-size: 2rem;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #dee2e6;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: #ffffff;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        .pagination {
            margin: 20px 0;
            text-align: center;
        }
        .pagination a {
            padding: 10px 20px;
            margin: 0 5px;
            text-decoration: none;
            color: #007bff;
            border: 1px solid #007bff;
            border-radius: 4px;
            transition: background-color 0.3s, color 0.3s;
        }
        .pagination a.active {
            background-color: #007bff;
            color: #ffffff;
            border: 1px solid #007bff;
        }
        .pagination a:hover {
            background-color: #0056b3;
            color: #ffffff;
        }
        .no-orders {
            text-align: center;
            font-size: 1.25rem;
            color: #6c757d;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Delivery Orders</h1>
    <?php
    if ($result->num_rows > 0) {
        echo "<table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User ID</th>
                        <th>Product ID</th>
                        <th>Quantity</th>
                        <th>Order Date</th>
                        <th>Order Status</th>
                    </tr>
                </thead>
                <tbody>";

        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row["order_id"]) . "</td>
                    <td>" . htmlspecialchars($row["user_id"]) . "</td>
                    <td>" . htmlspecialchars($row["product_id"]) . "</td>
                    <td>" . htmlspecialchars($row["quantity"]) . "</td>
                    <td>" . htmlspecialchars($row["order_date"]) . "</td>
                    <td>" . htmlspecialchars($row["orderStatus"]) . "</td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='no-orders'>No delivery orders found.</p>";
    }
    $con->close();
    ?>
</div>

</body>
</html>
