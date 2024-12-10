<?php
session_start();
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

// Check if user_id is set in the session
$user_id = isset($_SESSION['user']) ? (int)$_SESSION['user']['id'] : 0;

if ($user_id === 0) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit;
}

$product_name = "";
$price = "";
$description = "";
$image = "";
$availability = ""; // Default value

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    
    // Retrieve product details
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
        
        // Assign product details to variables
        $product_name = $product['name'];
        $price = $product['price'];
        $description = $product['description'];
        $image = isset($product['image']) ? $product['image'] : 'noimage.jpg'; // Default image path
        $availability = $product['availability']; // Assuming 'availability' is a column in your 'products' table
        
        $stmt->close();
    } else {
        echo "Product not found.";
        $con->close();
        exit;
    }
}

// Handling form submission to add product to cart
$cart_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['quantity'])) {
    if (is_numeric($_POST['quantity'])) {
        $quantity = intval($_POST['quantity']);
        
        // Check if product availability is "out of stock"
        if ($availability === 'out of stock') {
            $cart_message = "Product is out of stock and cannot be added to the cart.";
        } else {
            // Check if product is already in the cart for this user
            $sql_check = "SELECT * FROM carts WHERE product_id = ? AND user_id = ?";
            $stmt_check = $con->prepare($sql_check);
            $stmt_check->bind_param("ii", $product_id, $user_id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check && $result_check->num_rows > 0) {
                // Product already in cart, prevent adding more
                $cart_message = "Product is already in your cart.";
            } else {
                // Product not in cart, add new entry
                $sql_add = "INSERT INTO carts (product_id, user_id, quantity) VALUES (?, ?, ?)";
                $stmt_add = $con->prepare($sql_add);
                $stmt_add->bind_param("iii", $product_id, $user_id, $quantity);
                $stmt_add->execute();
                
                if ($stmt_add->affected_rows > 0) {
                    $cart_message = "Product added to cart.";
                } else {
                    $cart_message = "Failed to add product to cart.";
                }
                
                $stmt_add->close();
            }
            
            $stmt_check->close();
        }
    } else {
        $cart_message = "Invalid quantity.";
    }
}

// Handling form submission to add product to wishlist
$wishlist_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_wishlist'])) {
    // Check if product already exists in wishlist for this user
    $sql_check_wishlist = "SELECT * FROM wishlist WHERE product_id = ? AND user_id = ?";
    $stmt_check_wishlist = $con->prepare($sql_check_wishlist);
    $stmt_check_wishlist->bind_param("ii", $product_id, $user_id);
    $stmt_check_wishlist->execute();
    $result_check_wishlist = $stmt_check_wishlist->get_result();
    
    if ($result_check_wishlist && $result_check_wishlist->num_rows > 0) {
        $wishlist_message = "Product already in wishlist.";
    } else {
        // Product not in wishlist, add new entry
        $sql_add_wishlist = "INSERT INTO wishlist (product_id, user_id) VALUES (?, ?)";
        $stmt_add_wishlist = $con->prepare($sql_add_wishlist);
        $stmt_add_wishlist->bind_param("ii", $product_id, $user_id);
        $stmt_add_wishlist->execute();
        
        if ($stmt_add_wishlist->affected_rows > 0) {
            $wishlist_message = "Product added to wishlist.";
        } else {
            $wishlist_message = "Failed to add product to wishlist.";
        }
        
        $stmt_add_wishlist->close();
    }
    
    $stmt_check_wishlist->close();
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product_name); ?> Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            background-color: #f9f9f9;
        }
        .product-details {
            max-width: 800px;
            margin: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .product-details h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }
        .product-details img {
            max-width: 470px; /* Adjust the width to make the image smaller */
            height: auto;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .product-details table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .product-details th, .product-details td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .product-details th {
            background-color: #f2f2f2;
        }
        .action-links {
            margin-top: 20px;
        }
        .action-links a, .add-to-cart-btn, .add-to-wishlist-btn {
            display: inline-block;
            text-decoration: none;
            padding: 8px 16px;
            background-color: #007bff; /* Uniform button color */
            color: #fff;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }
        .action-links a:hover, .add-to-cart-btn:hover, .add-to-wishlist-btn:hover {
            background-color: #0056b3;
        }
        .add-to-cart-form, .add-to-wishlist-form {
            margin-top: 20px;
        }
        .availability-status {
            margin-top: 20px;
            font-size: 16px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="product-details">
        <h2><?php echo htmlspecialchars($product_name); ?></h2>
        
        <table>
            <tr>
                <th>Attribute</th>
                <th>Details</th>
            </tr>
            <tr>
                <td>Product Name</td>
                <td><?php echo htmlspecialchars($product_name); ?></td>
            </tr>
            <tr>
                <td>Price</td>
                <td>$<?php echo htmlspecialchars($price); ?></td>
            </tr>
            <tr>
                <td>Description</td>
                <td><?php echo htmlspecialchars($description); ?></td>
            </tr>
            <tr>
                <td>Image</td>
                <td>
                    <?php if ($image): ?>
                        <img src="<?php echo htmlspecialchars($image); ?>" alt="Product Image">
                    <?php else: ?>
                        No image available
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>Availability</td>
                <td><?php echo htmlspecialchars($availability); ?></td>
            </tr>
        </table>
        
        <!-- Form for adding to cart -->
        <?php if ($availability === 'In Stock'): ?>
            <form class="add-to-cart-form" method="POST" action="">
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" id="quantity" value="1" min="1" required>
                <button type="submit" class="add-to-cart-btn">Add to Cart</button>
            </form>
        <?php else: ?>
            <p>Product is out of stock and cannot be added to the cart.</p>
        <?php endif; ?>
        <?php if ($cart_message): ?>
            <p><?php echo htmlspecialchars($cart_message); ?></p>
        <?php endif; ?>
        
        <!-- Form for adding to wishlist -->
        <form class="add-to-wishlist-form" method="POST" action="">
            <input type="hidden" name="add_to_wishlist" value="1">
            <button type="submit" class="add-to-wishlist-btn">Add to Wishlist</button>
        </form>
        <?php if ($wishlist_message): ?>
            <p><?php echo htmlspecialchars($wishlist_message); ?></p>
        <?php endif; ?>
        
        <div class="action-links">
            <a href="add_review.php?product_id=<?php echo $product_id; ?>">Add Review</a>
            <a href="view_reviewsu.php?product_id=<?php echo $product_id; ?>">View Reviews</a>
            <a href="my_cart.php">My Cart</a>
            <a href="my_wishlist.php">My Wishlist</a>
            <a href="laptop_view.php">Back to Home Page</a>
        </div>
    </div>
</body>
</html>
