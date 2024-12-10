<?php
session_start(); // Start or resume session

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

// Function to sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(strip_tags($data));
}

// Function to get current user's ID from session
function get_user_id() {
    if (isset($_SESSION['user_id'])) {
        return $_SESSION['user_id'];
    }
    return null;
}

// Handling Add to Cart request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = sanitize_input($_POST['product_id']);
    $quantity = (int)$_POST['quantity'];
    $user_id = get_user_id(); // Retrieve user_id from session

    // Validate inputs and user session
    if ($product_id && $quantity > 0 && $user_id) {
        // Check if product exists
        $check_product_sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $con->prepare($check_product_sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            // Product exists, proceed to add to cart or update cart logic
            $cart_table = "carts"; // Replace with your actual cart table name

            // Check if the product is already in the cart for this user
            $check_cart_sql = "SELECT * FROM $cart_table WHERE user_id = ? AND product_id = ?";
            $stmt = $con->prepare($check_cart_sql);
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $result_cart = $stmt->get_result();

            if ($result_cart && $result_cart->num_rows > 0) {
                // Product already exists in cart, update quantity
                $update_cart_sql = "UPDATE $cart_table SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
                $stmt = $con->prepare($update_cart_sql);
                $stmt->bind_param("iii", $quantity, $user_id, $product_id);
                $stmt->execute();
                $stmt->close();
            } else {
                // Product does not exist in cart, insert new record
                $insert_cart_sql = "INSERT INTO $cart_table (user_id, product_id, quantity) VALUES (?, ?, ?)";
                $stmt = $con->prepare($insert_cart_sql);
                $stmt->bind_param("iii", $user_id, $product_id, $quantity);
                $stmt->execute();
                $stmt->close();
            }

            // Return success response or perform further actions as needed
            $response = array(
                'status' => 'success',
                'message' => 'Product added to cart successfully.'
            );
            echo json_encode($response);
        } else {
            // Product not found
            $response = array(
                'status' => 'error',
                'message' => 'Product not found.'
            );
            echo json_encode($response);
        }
    } else {
        // Invalid input or user not logged in
        $response = array(
            'status' => 'error',
            'message' => 'Invalid product or quantity, or user not logged in.'
        );
        echo json_encode($response);
    }

    exit; // End processing POST request
}

// Product listing and pagination logic
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
            $product_html .= "<a href='view_reviews.php?product_id=" . $row['id'] . "'>View Reviews</a>";
            
            // Form for adding to cart
            $product_html .= "<form class='add-to-cart-form' action='add_to_cart.php' method='post'>";
            $product_html .= "<input type='hidden' name='product_id' value='" . $row['id'] . "'>";
            $product_html .= "<input type='number' name='quantity' value='1' min='1' required>";
            $product_html .= "<button type='submit' class='add-to-cart-btn'>Add to Cart</button>";
            $product_html .= "</form>";
            
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
