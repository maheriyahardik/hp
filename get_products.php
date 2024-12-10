<?php
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

if (isset($_GET['subcategory_id']) && isset($_GET['page'])) {
    $subcategory_id = $_GET['subcategory_id'];
    $page = (int)$_GET['page'];
    $entries_per_page = 10;
    $start = ($page - 1) * $entries_per_page;
    
    // Retrieve total number of products for the subcategory
    $total_sql = "SELECT COUNT(*) as total FROM products WHERE subcategory_id = ?";
    $stmt = $con->prepare($total_sql);
    $stmt->bind_param("i", $subcategory_id);
    $stmt->execute();
    $total_result = $stmt->get_result();
    
    if ($total_result) {
        $total_row = $total_result->fetch_assoc();
        $total_products = $total_row['total'];
        $total_pages = ceil($total_products / $entries_per_page);
    } else {
        echo "Error fetching total number of products: " . $con->error;
        $total_pages = 1; // Default to 1 page if error occurs
    }
    
    // Retrieve products for the current page
    $sql = "SELECT p.id, p.name as product_name, p.price, p.description as product_description, p.image 
            FROM products p
            WHERE p.subcategory_id = ?
            LIMIT ?, ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("iii", $subcategory_id, $start, $entries_per_page);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $product_html = "<table>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>";
                        
    if ($result && $result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            $product_html .= "<tr>";
            $product_html .= "<td>" . $row["id"] . "</td>";
            $product_html .= "<td>" . $row["product_name"] . "</td>";
            $product_html .= "<td>" . $row["price"] . "</td>";
            $product_html .= "<td>" . $row["product_description"] . "</td>";
            
            // Check if product_image key exists in the $row array
            if (isset($row["image"])) {
                $product_html .= "<td><img src='" . $row["image"] . "' alt='Product Image'></td>";
            } else {
                $product_html .= "<td>No image available</td>";
            }
            
            // Action links for add review and view reviews
            $product_html .= "<td class='action-links'>";
            $product_html .= "<a href='add_review.php?product_id=" . $row['id'] . "'>Add Review</a>";
            $product_html .= "<a href='view_reviewsu.php?product_id=" . $row['id'] . "'>View Reviews</a>";
            $product_html .= "<a href='product_details.php?product_id=" . $row['id'] . "'>View details</a>";
            $product_html .= "</td>";
            
            $product_html .= "</tr>";
        }
    } else {
        $product_html .= "<tr><td colspan='6' class='no-products'>No products found</td></tr>";
    }
    $product_html .= "</table>";
    
    // Pagination HTML
    $pagination_html = "";
    if ($page > 1) {
        $pagination_html .= "<a href='#' data-page='" . ($page - 1) . "' class='prev'>Previous</a>";
    } else {
        $pagination_html .= "<a href='#' class='prev disabled'>Previous</a>";
    }
    
    if ($page < $total_pages) {
        $pagination_html .= "<a href='#' data-page='" . ($page + 1) . "' class='next'>Next</a>";
    } else {
        $pagination_html .= "<a href='#' class='next disabled'>Next</a>";
    }
    
    $response = array(
        'products' => $product_html,
        'pagination' => $pagination_html
    );
    
    echo json_encode($response);
    
    $stmt->close();
}

$con->close();
?>
