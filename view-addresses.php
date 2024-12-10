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

// Get user_id from session
$user_id = isset($_SESSION['user']) ? (int)$_SESSION['user']['id'] : 0;

// Fetch addresses for the current user
$sql = "SELECT * FROM addres WHERE user_id = ?";
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
    <title>View Addresses</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .addresses-container {
            max-width: 900px;
            margin: 30px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        .addresses-actions {
            margin: 20px 0;
        }
        .addresses-actions a {
            display: inline-block;
            padding: 10px 20px;
            text-decoration: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .addresses-actions a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="addresses-container">
        <h2>Your Addresses</h2>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Shipping Address</th>
                    <th>Billing Address</th>
                    <th>Billing State</th>
                    <th>Billing City</th>
                    <th>Billing Pincode</th>
                    <th>Shipping State</th>
                    <th>Shipping City</th>
                    <th>Shipping Pincode</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['shipping_address']); ?></td>
                        <td><?php echo htmlspecialchars($row['billing_address']); ?></td>
                        <td><?php echo htmlspecialchars($row['billing_state']); ?></td>
                        <td><?php echo htmlspecialchars($row['billing_city']); ?></td>
                        <td><?php echo htmlspecialchars($row['billing_pincode']); ?></td>
                        <td><?php echo htmlspecialchars($row['shipping_state']); ?></td>
                        <td><?php echo htmlspecialchars($row['shipping_city']); ?></td>
                        <td><?php echo htmlspecialchars($row['shipping_pincode']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>You have no addresses saved.</p>
        <?php endif; ?>

        <div class="addresses-actions">
            <a href="checkout.php">Back to Checkout</a>
        </div>
    </div>
</body>
</html>

<?php
$con->close();
?>
