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

// Retrieve total number of reviews
$total_sql = "SELECT COUNT(*) as total FROM reviews";
$total_result = $conn->query($total_sql);

if ($total_result) {
    $total_row = $total_result->fetch_assoc();
    $total_reviews = $total_row['total'];
    $total_pages = ceil($total_reviews / $entries_per_page);
} else {
    echo "Error fetching total number of reviews: " . $conn->error;
    $total_pages = 1; // Default to 1 page if error occurs
}

// Retrieve reviews for the current page with joined product information
$sql = "SELECT r.id, r.quality, r.price, r.value, r.name, r.summary, r.review, r.review_date, r.image, p.name as product_name
        FROM reviews r
        JOIN products p ON r.product_id = p.id
        LIMIT $start, $entries_per_page";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View Reviews</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 15px;
            text-align: left;
            border: 1px solid #dddddd;
        }
        table th {
            background-color: #343a40;
            color: #ffffff;
        }
        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        table tr:hover {
            background-color: #e9ecef;
        }
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a {
            color: #007bff;
            padding: 10px 20px;
            text-decoration: none;
            border: 1px solid #007bff;
            margin: 0 5px;
            border-radius: 4px;
            transition: background-color 0.3s, color 0.3s;
        }
        .pagination a.disabled {
            pointer-events: none;
            color: #aaaaaa;
            border-color: #aaaaaa;
        }
        .pagination a:hover:not(.disabled) {
            background-color: #007bff;
            color: #ffffff;
        }
        .back-to-home {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #ffffff;
            background-color: #007bff;
            border: 1px solid #007bff;
            padding: 10px 20px;
            border-radius: 4px;
            width: fit-content;
            margin: 20px auto;
            transition: background-color 0.3s;
        }
        .back-to-home:hover {
            background-color: #0056b3;
        }
        .no-reviews {
            text-align: center;
            font-style: italic;
            color: #666666;
        }
        .actions a {
            color: #dc3545;
            text-decoration: none;
            padding: 5px 10px;
            border: 1px solid #dc3545;
            border-radius: 4px;
            transition: background-color 0.3s, color 0.3s;
        }
        .actions a:hover {
            background-color: #dc3545;
            color: #ffffff;
        }
        .product-image {
            max-width: 100px; /* Increase the maximum width */
            height: auto;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Review List</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Image</th>
            <th>Quality</th>
            <th>Price</th>
            <th>Value</th>
            <th>Name</th>
            <th>Summary</th>
            <th>Review</th>
            <th>Review Date</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result && $result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["product_name"] . "</td>";
                echo "<td><img src='" . $row["image"] . "' alt='Product Image' class='product-image'></td>";
                echo "<td>" . $row["quality"] . "</td>";
                echo "<td>" . $row["price"] . "</td>";
                echo "<td>" . $row["value"] . "</td>";
                echo "<td>" . $row["name"] . "</td>";
                echo "<td>" . $row["summary"] . "</td>";
                echo "<td>" . $row["review"] . "</td>";
                echo "<td>" . $row["review_date"] . "</td>";
                echo "<td class='actions'><a href='delete_review.php?id=" . $row["id"] . "' onclick='return confirm(\"Are you sure you want to delete this review?\")'>Delete</a></td>";
                echo "</tr>";
            }
        } else {    
            echo "<tr><td colspan='11' class='no-reviews'>No reviews found</td></tr>";
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
<a href="admin-home.php" class="back-to-home">Back to Home</a>

</body>
</html>

<?php
$conn->close();
?>
