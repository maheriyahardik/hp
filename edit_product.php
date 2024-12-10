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

// Initialize variables
$product_id = isset($_GET['id']) ? $_GET['id'] : null;
$product_name = '';
$product_price = '';
$product_description = '';
$product_image = '';
$product_availability = '';
$message = '';

// Fetch product details from database
if ($product_id) {
    $sql = "SELECT * FROM products WHERE id = $product_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $product_name = $row['name'];
        $product_price = $row['price'];
        $product_description = $row['description'];
        $product_image = $row['image'];
        $product_availability = $row['availability'];
    } else {
        echo "Product not found.";
        exit;
    }
} else {
    echo "Invalid product ID.";
    exit;
}

// Handle form submission for updating product details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $updated_name = $_POST['name'];
    $updated_price = $_POST['price'];
    $updated_description = $_POST['description'];
    $updated_availability = $_POST['availability'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $message = "File is not an image.";
        } elseif ($_FILES["image"]["size"] > 5000000) {
            $message = "Sorry, your file is too large.";
        } elseif (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
            $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        } elseif (file_exists($target_file)) {
            $message = "Sorry, file already exists.";
        } elseif (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $product_image = $target_file;
        } else {
            $message = "Sorry, there was an error uploading your file.";
        }
    }

    // Update query
    if ($message === '') {
        $update_sql = !empty($product_image) ?
            "UPDATE products SET name='$updated_name', price='$updated_price', description='$updated_description', image='$product_image', availability='$updated_availability' WHERE id=$product_id" :
            "UPDATE products SET name='$updated_name', price='$updated_price', description='$updated_description', availability='$updated_availability' WHERE id=$product_id";

        if ($conn->query($update_sql) === TRUE) {
            $message = "Product updated successfully";
            // Redirect to product view page with success message
            header("Location: product-view.php?message=" . urlencode($message));
            exit;
        } else {
            $message = "Error updating product: " . $conn->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        form {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            color: #495057;
            margin-bottom: 8px;
        }

        .form-group input[type="text"], 
        .form-group textarea, 
        .form-group select, 
        .form-group input[type="file"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-group textarea {
            height: 120px;
            resize: vertical;
        }

        .form-group img {
            max-width: 150px;
            max-height: 150px;
            margin-top: 10px;
            display: block;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .form-group .image-preview {
            margin-top: 10px;
        }

        .form-group .btn-submit {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
        }

        .form-group .btn-submit:hover {
            background-color: #0056b3;
        }

        .form-group .btn-reset {
            background-color: #6c757d;
            color: #ffffff;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            margin-left: 10px;
        }

        .form-group .btn-reset:hover {
            background-color: #5a6268;
        }

        .message {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Product</h2>

    <?php if (!empty($message)) { ?>
        <div class="message <?php echo strpos($message, 'Error') === false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Product Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product_name); ?>" required>
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="text" id="price" name="price" value="<?php echo htmlspecialchars($product_price); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($product_description); ?></textarea>
        </div>
        <div class="form-group">
            <label for="availability">Availability:</label>
            <select id="availability" name="availability" required>
                <option value="In Stock" <?php if ($product_availability == 'In Stock') echo 'selected'; ?>>In Stock</option>
                <option value="Out of Stock" <?php if ($product_availability == 'Out of Stock') echo 'selected'; ?>>Out of Stock</option>
            </select>
        </div>
        <div class="form-group">
            <label for="image">Product Image:</label>
            <?php if (!empty($product_image)) { ?>
                <div class="image-preview">
                    <img src="<?php echo htmlspecialchars($product_image); ?>" alt="Product Image">
                </div>
            <?php } else { ?>
                <div class="image-preview">
                    No image available
                </div>
            <?php } ?>
            <input type="file" id="image" name="image">
        </div>
        <div class="form-group">
            <input type="submit" class="btn-submit" value="Update Product">
            <a href="product-view.php" class="btn-reset">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>

<?php
$conn->close();
?>
