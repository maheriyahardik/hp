<?php
session_start();
include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['category_name']) && !empty($_POST['description'])) {
        $category_name = mysqli_real_escape_string($con, $_POST['category_name']);
        $description = mysqli_real_escape_string($con, $_POST['description']);

        $query = "INSERT INTO categories (name, description) VALUES ('$category_name', '$description')";
        if (mysqli_query($con, $query)) {
            $message = "Category added successfully!";
        } else {
            $error = "Error adding category: " . mysqli_error($con);
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
    <title>Add Category</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type=text], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
            button[type=submit], .back-btn {
                background-color: #4CAF50;
                color: white;
                padding: 13px 1px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 16px;
                display: block;
                width: 90%;
                margin-top: 10px;
                text-align: center;
                text-decoration: none;
            }
        button[type=submit]:hover, .back-btn:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
        .message {
            color: green;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add Category</h1>
        <?php if (isset($message)) { echo "<p class='message'>$message</p>"; } ?>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form action="create_category.php" method="post">
            <label for="category_name">Category Name:</label>
            <input type="text" id="category_name" name="category_name" required><br>
            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea><br>
            <button type="submit">Add Category</button>
            <a href="admin-home.php" class="back-btn">Back to Home</a>
        </form>
    </div>
</body>
</html>
