<?php
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

// Pagination variables
$entries_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $entries_per_page;

// Search variables
$search_id = isset($_GET['search_id']) ? $conn->real_escape_string($_GET['search_id']) : '';

// Retrieve total number of products with search condition
$total_sql = "SELECT COUNT(*) as total FROM products WHERE id LIKE '%$search_id%'";
$total_result = $conn->query($total_sql);

if ($total_result) {
    $total_row = $total_result->fetch_assoc();
    $total_products = $total_row['total'];
    $total_pages = ceil($total_products / $entries_per_page);
} else {
    echo "Error fetching total number of products: " . $conn->error;
    $total_pages = 1; // Default to 1 page if error occurs
}

// Retrieve products for the current page with search condition and joined category and subcategory information
$sql = "SELECT p.id, c.name as category, s.name as subcategory, p.name as product_name, p.price, p.description as product_description, p.image, p.availability
        FROM products p
        JOIN subcategories s ON p.subcategory_id = s.id
        JOIN categories c ON s.category_id = c.id
        WHERE p.id LIKE '%$search_id%'
        LIMIT $start, $entries_per_page";
$result = $conn->query($sql);
if (!$result) {
    echo "Error executing query: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Products</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333333;
        }

        form {
            margin-bottom: 20px;
            text-align: center;
        }

        form input[type="text"] {
            padding: 12px;
            font-size: 16px;
            border: 1px solid #cccccc;
            border-radius: 5px;
            width: 220px;
            margin-right: 10px;
        }

        form button {
            padding: 12px 24px;
            font-size: 16px;
            color: #ffffff;
            background-color: #28a745;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        form button:hover {
            background-color: #218838;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 16px;
            text-align: left;
            border: 1px solid #e0e0e0;
        }

        table th {
            background-color: #007bff;
            color: #ffffff;
            font-weight: bold;
        }

        table td {
            background-color: #ffffff;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #e9ecef;
        }

        table td img {
            max-width: 120px;
            max-height: 120px;
            display: block;
            margin: auto;
            border-radius: 8px;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            color: #007bff;
            padding: 12px 20px;
            text-decoration: none;
            border: 1px solid #007bff;
            border-radius: 5px;
            margin: 0 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .pagination a.disabled {
            pointer-events: none;
            color: #6c757d;
            border-color: #6c757d;
        }

        .pagination a:hover:not(.disabled) {
            background-color: #007bff;
            color: #ffffff;
        }

        .back-to-home {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #ffffff;
            background-color: #007bff;
            border: 1px solid #007bff;
            padding: 12px 20px;
            border-radius: 5px;
            width: fit-content;
            transition: background-color 0.3s, color 0.3s;
        }

        .back-to-home:hover {
            background-color: #0056b3;
            color: #ffffff;
        }

        .no-products {
            text-align: center;
            font-style: italic;
            color: #6c757d;
        }

        .action-links {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .action-links a {
            padding: 8px 16px;
            text-decoration: none;
            color: #007bff;
            border: 1px solid #007bff;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .action-links a:hover {
            background-color: #007bff;
            color: #ffffff;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            table th, table td {
                padding: 12px;
            }

            .pagination a {
                padding: 10px 16px;
                font-size: 14px;
            }

            .back-to-home {
                font-size: 14px;
                padding: 10px 16px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Product List</h2>
    <!-- Search Form -->
    <form method="GET" action="">
        <input type="text" name="search_id" value="<?php echo htmlspecialchars($search_id); ?>" placeholder="Search by ID" />
        <button type="submit">Search</button>
    </form>
    <a href="admin_view_reviews.php" class="back-to-home">Manage Reviews</a>
    <a href="admin-home.php" class="back-to-home">Back To Home</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Category</th>
            <th>Subcategory</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Image</th>
            <th>Availability</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["category"] . "</td>";
                echo "<td>" . $row["subcategory"] . "</td>";
                echo "<td>" . $row["product_name"] . "</td>";
                echo "<td>" . $row["price"] . "</td>";
                echo "<td>" . $row["product_description"] . "</td>";
                
                if (isset($row["image"]) && !empty($row["image"])) {
                    echo "<td><img src='" . $row["image"] . "' alt='Product Image'></td>";
                } else {
                    echo "<td>No image available</td>";
                }
                
                echo "<td>" . $row["availability"] . "</td>";
                
                echo "<td class='action-links'>";
                echo "<a href='edit_product.php?id=" . $row['id'] . "'>Edit</a>";
                echo "<a href='delete_product.php?id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this product?\")'>Delete</a>";
                echo "</td>";

                echo "</tr>";
            }
        } else {    
            echo "<tr><td colspan='9' class='no-products'>No products found</td></tr>";
        }
        ?>
    </table>

    <div class="pagination">
        <?php if ($page > 1) { ?>
            <a href="?page=<?php echo $page - 1; ?>&search_id=<?php echo htmlspecialchars($search_id); ?>" class="prev">Previous</a>
        <?php } else { ?>
            <a href="#" class="prev disabled">Previous</a>
        <?php } ?>

        <?php if ($page < $total_pages) { ?>
            <a href="?page=<?php echo $page + 1; ?>&search_id=<?php echo htmlspecialchars($search_id); ?>" class="next">Next</a>
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
