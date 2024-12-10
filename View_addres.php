<?php
// db.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hp";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>


<!-- fetch_data.php -->
<?php
include 'connection.php';

$user_id = isset($_SESSION['user']) ? $_SESSION['admin'] : 0;

if ($user_id === 0) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit;
}
$sql = "SELECT * FROM addres";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Addresses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 3px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            background: #fff;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .no-records {
            text-align: center;
            color: #555;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Address Information</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Shipping Address</th>
                    <th>Billing Address</th>
                    <th>Billing State</th>
                    <th>Billing City</th>
                    <th>Billing Pincode</th>
                    <th>Shipping State</th>
                    <th>Shipping Pincode</th>
                    <th>Created At</th>
                    <th>User ID</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["shipping_address"] . "</td>";
                        echo "<td>" . $row["billing_address"] . "</td>";
                        echo "<td>" . $row["billing_state"] . "</td>";
                        echo "<td>" . $row["billing_city"] . "</td>";
                        echo "<td>" . $row["billing_pincode"] . "</td>";
                        echo "<td>" . $row["shipping_state"] . "</td>";
                        echo "<td>" . $row["shipping_pincode"] . "</td>";
                        echo "<td>" . $row["created_at"] . "</td>";
                        echo "<td>" . $row["user_id"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11' class='no-records'>No records found</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
