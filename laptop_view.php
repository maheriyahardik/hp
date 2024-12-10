<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
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
$user_id = isset($_SESSION['user']) ? (int)$_SESSION['user']['id'] : 0;

if ($user_id === 0) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit;
}

// Retrieve categories and subcategories for dropdown menus
$categories_sql = "SELECT * FROM categories";
$categories_result = $conn->query($categories_sql);

$subcategories_sql = "SELECT * FROM subcategories";
$subcategories_result = $conn->query($subcategories_sql);

// Pagination variables
$entries_per_page = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $entries_per_page;

// Default SQL query to retrieve all products
$sql = "SELECT p.id, c.name as category, s.name as subcategory, p.name as product_name, p.price, p.description as product_description, p.image 
        FROM products p
        JOIN subcategories s ON p.subcategory_id = s.id
        JOIN categories c ON s.category_id = c.id";

// Filtering based on category and subcategory
if (isset($_GET['category']) && $_GET['category'] != 'all') {
    $category_id = $_GET['category'];
    $sql .= " WHERE c.id = $category_id";
    if (isset($_GET['subcategory']) && $_GET['subcategory'] != 'all') {
        $subcategory_id = $_GET['subcategory'];
        $sql .= " AND s.id = $subcategory_id";
    }
}

$sql .= " LIMIT $start, $entries_per_page";

$result = $conn->query($sql);

// Retrieve total number of products
$total_sql = "SELECT COUNT(*) as total FROM products";
if (isset($_GET['category']) && $_GET['category'] != 'all') {
    $category_id = $_GET['category'];
    $total_sql .= " JOIN subcategories s ON products.subcategory_id = s.id
                   JOIN categories c ON s.category_id = c.id
                   WHERE c.id = $category_id";
    if (isset($_GET['subcategory']) && $_GET['subcategory'] != 'all') {
        $subcategory_id = $_GET['subcategory'];
        $total_sql .= " AND s.id = $subcategory_id";
    }
}
$total_result = $conn->query($total_sql);

if ($total_result) {
    $total_row = $total_result->fetch_assoc();
    $total_products = $total_row['total'];
    $total_pages = ceil($total_products / $entries_per_page);
} else {
    echo "Error fetching total number of products: " . $conn->error;
    $total_pages = 1; // Default to 1 page if error occurs
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <style>
        .inner_posituong .header 
        {
         position: inherit;
        background: cornflowerblue;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .productlistdv{
            
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #00796b;
        }

        .product-list {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .product-card {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product-card img {
            max-width: 100%;
            max-height: 200px;
            margin-bottom: 15px;
            object-fit: contain;
        }

        .product-card h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #004d40;
        }

        .product-card p {
            margin: 5px 0;
            color: #00796b;
        }

        .product-card .view-button {
            background-color: #00796b;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s ease;
            margin-top: 10px;
            display: inline-block;
            width: 100%;
            box-sizing: border-box;
        }

        .product-card .view-button:hover {
            background-color: #004d40;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            color: #00796b;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin: 0 5px;
            border-radius: 4px;
        }

        .pagination a.disabled {
            pointer-events: none;
            color: #ccc;
        }

        .pagination a:hover {
            background-color: #e0f7fa;
        }

        .back-to-home {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #00796b;
            border: 1px solid #ccc;
            padding: 8px 12px;
            border-radius: 4px;
            width: fit-content;
            margin: 20px auto;
        }

        .back-to-home:hover {
            background-color: #e0f7fa;
        }

        .no-products {
            text-align: center;
            font-style: italic;
            color: #999;
        }
        body {
        font-family: Arial, sans-serif;
        background-color: #e0f7fa;
        margin: 0;
        padding: 0;
        color: #333;
        }
        .container {
        margin: 20px auto;
        padding: 20px;
        background-color: #ffffff;
        border: 1px solid #ccc;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

h2 {
    text-align: center;
    color: #00796b;
}

.product-list {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.product-card {
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    padding: 15px;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.product-card img {
    max-width: 100%;
    max-height: 200px;
    margin-bottom: 15px;
    object-fit: contain;
}

.product-card h3 {
    font-size: 18px;
    margin-bottom: 10px;
    color: #004d40;
}

.product-card p {
    margin: 5px 0;
    color: #00796b;
}

.product-card .view-button {
    background-color: #00796b;
    color: #fff;
    padding: 10px;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
    transition: background-color 0.3s ease;
    margin-top: 10px;
    display: inline-block;
    width: 100%;
    box-sizing: border-box;
}

.product-card .view-button:hover {
    background-color: #004d40;
}

.pagination {
    text-align: center;
    margin-top: 20px;
}

.pagination a {
    color: #00796b;
    padding: 8px 16px;
    text-decoration: none;
    border: 1px solid #ddd;
    margin: 0 5px;
    border-radius: 4px;
}

.pagination a.disabled {
    pointer-events: none;
    color: #ccc;
}

.pagination a:hover {
    background-color: #e0f7fa;
}

.back-to-home {
    display: block;
    text-align: center;
    margin-top: 20px;
    text-decoration: none;
    color: #00796b;
    border: 1px solid #ccc;
    padding: 8px 12px;
    border-radius: 4px;
    width: fit-content;
    margin: 20px auto;
}

.back-to-home:hover {
    background-color: #e0f7fa;
}

.no-products {
    text-align: center;
    font-style: italic;
    color: #999;
}

form {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 20px;
}

form label {
    margin: 10px 0 5px;
    font-size: 16px;
    color: #00796b;
}

form select {
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #fff;
    color: #333;
    margin: 5px 0;
    width: 200px;
    max-width: 100%;
}

form button {
    background-color: #00796b;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

form button:hover {
    background-color: #004d40;
}

    </style>
</head>

<body>

<div class="container-fluid productlistdv">
    <h2>Product List</h2>

    <form id="filterForm" method="GET">
        <label for="category">Select Category:</label>
        <select id="category" name="category">
            <option value="all">All Categories</option>
            <?php
            if ($categories_result && $categories_result->num_rows > 0) {
                while ($cat_row = $categories_result->fetch_assoc()) {
                    echo "<option value='" . $cat_row['id'] . "'" . (isset($_GET['category']) && $_GET['category'] == $cat_row['id'] ? ' selected' : '') . ">" . $cat_row['name'] . "</option>";
                }
            }
            ?>
        </select>

        <label for="subcategory">Select Subcategory:</label>
        <select id="subcategory" name="subcategory">
            <option value="all">All Subcategories</option>
            <?php
            if ($subcategories_result && $subcategories_result->num_rows > 0) {
                while ($subcat_row = $subcategories_result->fetch_assoc()) {
                    echo "<option value='" . $subcat_row['id'] . "'" . (isset($_GET['subcategory']) && $_GET['subcategory'] == $subcat_row['id'] ? ' selected' : '') . ">" . $subcat_row['name'] . "</option>";
                }
            }
            ?>
        </select>

        <button type="submit">Filter</button>
    </form>

    <div class="product-list">
        <?php
        if ($result && $result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<div class='product-card'>";
                echo "<h3>" . $row["product_name"] . "</h3>";
                echo "<p><strong>Category:</strong> " . $row["category"] . "</p>";
                echo "<p><strong>Subcategory:</strong> " . $row["subcategory"] . "</p>";
                echo "<p><strong>Description:</strong> " . $row["product_description"] . "</p>";

                // Display image if available
                if (isset($row["image"])) {
                    echo "<img src='" . $row["image"] . "' alt='Product Image'>";
                } else {
                    echo "<p>No image available</p>";
                }
                echo "<p><strong>Price:</strong> $" . $row["price"] . "</p>";
                // echo "<a href='add_review.php?product_id=" . $row["id"] . "' class='view-button'>Add Review</a>";
                // echo "<a href='view_reviewsu.php?product_id=" . $row["id"] . "' class='view-button'>View Reviews</a>";
                echo "<a href='product_details.php?product_id=" . $row["id"] . "' class='view-button'>View Details</a>";
                echo "</div>";
            }
        } else {
            echo "<p class='no-products'>No products found</p>";
        }
        ?>
    </div>

    <div class="pagination">
        <?php if ($page > 1) { ?>
            <a href="?page=<?php echo $page - 1; ?>&category=<?php echo isset($_GET['category']) ? $_GET['category'] : 'all'; ?>&subcategory=<?php echo isset($_GET['subcategory']) ? $_GET['subcategory'] : 'all'; ?>" class="prev">Previous</a>
        <?php } else { ?>
            <a href="#" class="prev disabled">Previous</a>
        <?php } ?>

        <?php if ($page < $total_pages) { ?>
            <a href="?page=<?php echo $page + 1; ?>&category=<?php echo isset($_GET['category']) ? $_GET['category'] : 'all'; ?>&subcategory=<?php echo isset($_GET['subcategory']) ? $_GET['subcategory'] : 'all'; ?>" class="next">Next</a>
        <?php } else { ?>
            <a href="#" class="next disabled">Next</a>
        <?php } ?>
    </div>
</div>
<a href="laptop_view.php" class="back-to-home">Back to Laptop Page</a>
<a href="index.php" class="back-to-home">Back to Home</a>

</body>
</html>

<?php
$conn->close();
?>