<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hp";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Check if admin is logged in
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}


$sql = "SELECT id, user_id, shipping_address, billing_address, billing_state, billing_city, billing_pincode, shipping_state, shipping_city, 
shipping_pincode, created_at FROM addres";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View All Addresses</title>
    <link rel="stylesheet" href="styles.css">
    <style>body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.container {
    width: 80%;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 8px;
    text-align: left;
}

th {
    background-color: #f2f2f2;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #f1f1f1;
}

    </style>
</head>
<body>
    <div class="container">
        <h1>All Addresses</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Shipping Address</th>
                    <th>Billing Address</th>
                    <th>Billing State</th>
                    <th>Billing City</th>
                    <th>Billing Pincode</th>
                    <th>Shipping State</th>
                    <th>Shipping City</th>
                    <th>Shipping Pincode</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["user_id"] . "</td>";
                        echo "<td>" . $row["shipping_address"] . "</td>";
                        echo "<td>" . $row["billing_address"] . "</td>";
                        echo "<td>" . $row["billing_state"] . "</td>";
                        echo "<td>" . $row["billing_city"] . "</td>";
                        echo "<td>" . $row["billing_pincode"] . "</td>";
                        echo "<td>" . $row["shipping_state"] . "</td>";
                        echo "<td>" . $row["shipping_city"] . "</td>";
                        echo "<td>" . $row["shipping_pincode"] . "</td>";
                        echo "<td>" . $row["created_at"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11'>No addresses found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
    