<?php
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

// Pagination variables
$entries_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $entries_per_page;

// Retrieve total number of products
$total_sql = "SELECT COUNT(*) as total FROM products";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_products = $total_row['total'];
$total_pages = ceil($total_products / $entries_per_page);

// Retrieve products for the current page with joined category and subcategory information
$sql = "SELECT p.id, c.name as category, s.name as subcategory, p.name as product_name, p.price, p.description as product_description, p.image1 as product_image1 
        FROM products p
        JOIN subcategories s ON p.subcategory_id = s.id
        JOIN categories c ON s.category_id = c.id
        LIMIT $start, $entries_per_page";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 12px;
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
        td {
            vertical-align: middle;
        }
        .no-products {
            text-align: center;
            font-style: italic;
            color: #888;
        }
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a {
            padding: 8px 16px;
            margin: 0 4px;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
        }
        .pagination a:hover {
            background-color: #0056b3;
        }
        .pagination a.disabled {
            background-color: #ccc;
            pointer-events: none;
        }
        .actions {
            text-align: center;
        }
        .actions a {
            margin: 0 5px;
            padding: 6px 12px;
            text-decoration: none;
            color: #007bff;
            border: 1px solid #007bff;
            border-radius: 4px;
        }
        .actions a:hover {
            background-color: #007bff;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Product List</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Category</th>
            <th>Subcategory</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["category"] . "</td>";
                echo "<td>" . $row["subcategory"] . "</td>";
                echo "<td>" . $row["product_name"] . "</td>";
                echo "<td>" . $row["price"] . "</td>";
                echo "<td>" . $row["product_description"] . "</td>";
                echo "<td class='actions'>";
                echo "<a href='edit_product.php?id=" . $row["id"] . "'>Edit</a>";
                echo "<a href='delete_product.php?id=" . $row["id"] . "' onclick='return confirm(\"Are you sure you want to delete this product?\");'>Delete</a>";
                echo "</td>";
                echo "</tr>";
            }
        } else {    
            echo "<tr><td colspan='7' class='no-products'>No products found</td></tr>";
        }
        ?>
    </table>

    <div class="pagination">
        <?php if ($page > 1) { ?>
            <a href="?page=<?php echo $page - 1; ?>" class="prev">Previous</a>
        <?php } else { ?>
            <a href="#" class="prev disabled">Previous</a>
        <?php } ?>

        <?php if ($page < $total_pages) { ?>
            <a href="?page=<?php echo $page + 1; ?>" class="next">Next</a>
        <?php } else { ?>
            <a href="#" class="next disabled">Next</a>
        <?php } ?>
    </div>
</div>

</body>
</html>

<?php
$conn->close();
?>
