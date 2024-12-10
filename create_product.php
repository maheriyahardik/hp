<?php
session_start();
include "connection.php";

// Fetch categories for dropdown
$query_categories = "SELECT id, name FROM categories";
$result_categories = mysqli_query($con, $query_categories);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && !empty($_POST['product_name']) && !empty($_POST['product_price']) && !empty($_POST['product_description']) && isset($_POST['product_availability'])) {
        $category_id = $_POST['category_id'];
        $subcategory_id = $_POST['subcategory_id'];
        $product_name = mysqli_real_escape_string($con, $_POST['product_name']);
        $product_price = mysqli_real_escape_string($con, $_POST['product_price']);
        $product_description = mysqli_real_escape_string($con, $_POST['product_description']);
        $availability = mysqli_real_escape_string($con, $_POST['product_availability']);

        // Example for handling file upload (product image)
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["product_image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Initialize upload message and error variables
        $upload_message = '';
        $upload_error = '';

        // Check if image file is a actual image or fake image
        if (isset($_POST["submit"])) {
            $check = getimagesize($_FILES["product_image"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $upload_error = "File is not an image.";
                $uploadOk = 0;
            }
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            $upload_error = "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["product_image"]["size"] > 500000) {
            $upload_error = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $upload_error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $upload_error = "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                $upload_message = "The file " . htmlspecialchars(basename($_FILES["product_image"]["name"])) . " has been uploaded.";
            } else {
                $upload_error = "Sorry, there was an error uploading your file.";
            }
        }

        // Insert product into database
        $query = "INSERT INTO products (category_id, subcategory_id, name, price, description, image, availability) VALUES ('$category_id', '$subcategory_id', '$product_name', '$product_price', '$product_description', '$target_file', '$availability')";
        if (mysqli_query($con, $query)) {
            $message = "Product added successfully!";
        } else {
            $error = "Error adding product: " . mysqli_error($con);
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type=text],
        textarea,
        select,
        input[type=file] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #218838;
        }
        .back-btn {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: #555;
            border: 1px solid #ccc;
            padding: 8px 12px;
            border-radius: 4px;
            margin-right: 10px;
        }
        .back-btn:hover {
            background-color: #f0f0f0;
        }
        .message {
            color: green;
            margin-top: 10px;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        .upload-message {
            color: #28a745;
            margin-top: 10px;
            font-weight: bold;
        }
        .upload-error {
            color: #dc3545;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
    <script>
        // Function to populate subcategories based on selected category
        function populateSubcategories() {
            var category_id = document.getElementById("category_id").value;
            var subcategorySelect = document.getElementById("subcategory_id");

            // Clear existing options
            subcategorySelect.innerHTML = "";

            // AJAX request to fetch subcategories based on category_id
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "get_subcategories.php?category_id=" + category_id, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var subcategories = JSON.parse(xhr.responseText);

                    // Populate subcategory select options
                    subcategories.forEach(function(subcategory) {
                        var option = document.createElement("option");
                        option.value = subcategory.id;
                        option.textContent = subcategory.name;
                        subcategorySelect.appendChild(option);
                    });
                }
            };
            xhr.send();
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Add Product</h1>
        <a href="view_categories.php" class="back-btn">View Categories</a>
        <a href="create_category.php" class="back-btn">Add Category</a>
        <a href="create_subcategory.php" class="back-btn">Add Subcategory</a>
        <a href="view_subcategories.php" class="back-btn">View Subcategories</a>
        
        <?php if (isset($message)) { echo "<p class='message'>$message</p>"; } ?>
        <?php if (isset($upload_message)) { echo "<p class='upload-message'>$upload_message</p>"; } ?>
        <?php if (isset($upload_error)) { echo "<p class='upload-error'>$upload_error</p>"; } ?>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form action="create_product.php" method="post" enctype="multipart/form-data">
            <label for="category_id">Category:</label>
            <select id="category_id" name="category_id" required onchange="populateSubcategories()">
                <!-- Populate this with options from categories table -->
                <?php
                while ($row_category = mysqli_fetch_assoc($result_categories)) {
                    echo "<option value='{$row_category['id']}'>{$row_category['name']}</option>";
                }
                ?>
            </select><br>
            <label for="subcategory_id">Subcategory:</label>
            <select id="subcategory_id" name="subcategory_id" required>
                <!-- Subcategories will be populated dynamically based on selected category -->
            </select><br>
            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name" required><br>
            <label for="product_price">Product Price:</label>
            <input type="text" id="product_price" name="product_price" required><br>
            <label for="product_availability">Availability:</label>
            <select id="product_availability" name="product_availability" required>
                <option value="In Stock">In Stock</option>
                <option value="Out of Stock">Out of Stock</option>
            </select><br>
            <label for="product_description">Product Description:</label>
            <textarea id="product_description" name="product_description" required></textarea><br>
            <label for="product_image">Product Image:</label>
            <input type="file" id="product_image" name="product_image" accept="image/*"><br>
            <button type="submit">Create Product</button>
            <a href="admin-home.php" class="back-btn">Back to Home</a>
        </form>
    </div>
</body>
</html>
