<?php
session_start();
include "connection.php";

// Check if the user is logged in or has admin rights (adjust as needed)

// Fetch subcategories from the database
$query = "SELECT subcategories.id, subcategories.name, categories.name AS category_name 
          FROM subcategories 
          JOIN categories ON subcategories.category_id = categories.id";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Error fetching subcategories: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Subcategories</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            text-align: center;
        }
        .back-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Subcategories</h1>
        <table>
            <tr>
                <th>ID</th>
                <th>Subcategory Name</th>
                <th>Category Name</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        <a href="admin-home.php" class="back-btn">Back to Home</a>
    </div>
</body>
</html>

<?php
mysqli_close($con);
?>
