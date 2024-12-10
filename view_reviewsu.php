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

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

// Pagination variables
$entries_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $entries_per_page;

// Retrieve total number of reviews for the specific product
$total_sql = "SELECT COUNT(*) as total FROM reviews WHERE product_id = ?";
$stmt = $conn->prepare($total_sql);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$total_result = $stmt->get_result();

if ($total_result) {
    $total_row = $total_result->fetch_assoc();
    $total_reviews = $total_row['total'];
    $total_pages = ceil($total_reviews / $entries_per_page);
} else {
    echo "Error fetching total number of reviews: " . $conn->error;
    $total_pages = 1; // Default to 1 page if error occurs
}

// Retrieve reviews for the current page
$sql = "SELECT * FROM reviews WHERE product_id = ? LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iii', $product_id, $start, $entries_per_page);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Product Reviews</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1400px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
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
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a {
            color: #000;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin: 0 5px;
        }
        .pagination a.disabled {
            pointer-events: none;
            color: #aaa;
        }
        .pagination a:hover {
            background-color: #f2f2f2;
        }
        .back-to-home {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #555;
            border: 1px solid #ccc;
            padding: 8px 12px;
            border-radius: 4px;
            width: fit-content;
            margin: 20px auto;
        }
        .back-to-home:hover {
            background-color: #f0f0f2;
        }
        .no-reviews {
            text-align: center;
            font-style: italic;
            color: #999;
        }
        .review-image {
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
        }
    </style>
</head>
<body>

<div class="container">
<a href="add_review.php?product_id=<?php echo $product_id; ?>">Add Review</a>
    <h2>Reviews for Product ID: <?php echo htmlspecialchars($product_id); ?></h2>

    <?php
    if ($result && $result->num_rows > 0) {
        echo '<table>';
        echo '<tr><th>Name</th><th>Quality</th><th>Price</th><th>Value</th><th>Summary</th><th>Review</th><th>Review Date</th><th>Image</th></tr>';
        while($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row["name"]) . '</td>';
            echo '<td>' . htmlspecialchars($row["quality"]) . '</td>';
            echo '<td>' . htmlspecialchars($row["price"]) . '</td>';
            echo '<td>' . htmlspecialchars($row["value"]) . '</td>';
            echo '<td>' . htmlspecialchars($row["summary"]) . '</td>';
            echo '<td>' . nl2br(htmlspecialchars($row["review"])) . '</td>';
            echo '<td>' . htmlspecialchars($row["review_date"]) . '</td>';
            if ($row["image"]) {
                echo '<td><img src="' . htmlspecialchars($row["image"]) . '" class="review-image" alt="Review Image"></td>';
            } else {
                echo '<td>No image</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    } else {    
        echo "<p class='no-reviews'>No reviews found</p>";
    }
    ?>

    <div class="pagination">
        <?php if ($page > 1) { ?>
            <a href="?product_id=<?php echo htmlspecialchars($product_id); ?>&page=<?php echo $page - 1; ?>" class="prev">Previous</a>
        <?php } else { ?>
            <a href="#" class="prev disabled">Previous</a>
        <?php } ?>

        <?php if ($page < $total_pages) { ?>
            <a href="?product_id=<?php echo htmlspecialchars($product_id); ?>&page=<?php echo $page + 1; ?>" class="next">Next</a>
        <?php } else { ?>
            <a href="#" class="next disabled">Next</a>
        <?php } ?>
    </div>
</div>
<a href="laptop_view.php" class="back-to-home">Back to Product List</a>

</body>
</html>

<?php
$conn->close();
?>
