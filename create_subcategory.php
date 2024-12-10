<?php
session_start();
include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['category_id']) && !empty($_POST['subcategory_name'])) {
        $category_id = mysqli_real_escape_string($con, $_POST['category_id']);
        $subcategory_name = mysqli_real_escape_string($con, $_POST['subcategory_name']);

        $query = "INSERT INTO subcategories (category_id, name) VALUES ('$category_id', '$subcategory_name')";
        if (mysqli_query($con, $query)) {
            $message = "Subcategory added successfully!";
        } else {
            $error = "Error adding subcategory: " . mysqli_error($con);
        }
    } else {
        $error = "All fields are required.";
    }
}

$categories = mysqli_query($con, "SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Subcategory</title>
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
        select, input[type=text] {
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
        button[type=submit], .back-btn:hover {
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
        <h1>Add Subcategory</h1>
        <?php if (isset($message)) { echo "<p class='message'>$message</p>"; } ?>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form action="create_subcategory.php" method="post">
            <label for="category_id">Category:</label>
            <select id="category_id" name="category_id" required>
                <option value="">Select Category</option>
                <?php while ($row = mysqli_fetch_assoc($categories)) { ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                <?php } ?>
            </select><br>
            <label for="subcategory_name">Subcategory Name:</label>
            <input type="text" id="subcategory_name" name="subcategory_name" required><br>
            <button type="submit">Add Subcategory</button>
            <a href="admin-home.php" class="back-btn">Back to Home Page</a>
        </form>
    </div>
</body>
</html>
