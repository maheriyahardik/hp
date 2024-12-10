<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #343a40;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #343a40;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }
        .no-orders {
            text-align: center;
            font-size: 18px;
            color: #6c757d;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <?php
    // Include database connection
    include('connection.php');

    // Start session if needed
    session_start();

    // Check if the user is an admin
    // Add your admin check logic here

    // Fetch pending orders from the database
    $sql = "SELECT orders.id AS order_id, orders.user_id, orders.order_date, orders.orderStatus, 
                   order_items.product_id, order_items.quantity 
            FROM orders 
            JOIN order_items ON orders.id = order_items.order_id 
            WHERE orders.orderStatus = 'Pending'"; // Adjust the condition based on your database structure

    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        echo "<h1>Pending Orders</h1>";
        echo "<table>
                <tr>
                    <th>Order ID</th>
                    <th>User ID</th>
                    <th>Product ID</th>
                    <th>Quantity</th>
                    <th>Order Date</th>
                    <th>Order Status</th>
                </tr>";

        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["order_id"]. "</td>
                    <td>" . $row["user_id"]. "</td>
                    <td>" . $row["product_id"]. "</td>
                    <td>" . $row["quantity"]. "</td>
                    <td>" . $row["order_date"]. "</td>
                    <td>" . $row["orderStatus"]. "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='no-orders'>No pending orders found.</div>";
    }

    $con->close();
    ?>
</div>

</body>
</html>
