<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$user_id = isset($_SESSION['user']) ? (int)$_SESSION['user']['id'] : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = (int)$_POST['product_id'];
    $quality = (int)$_POST['quality'];
    $price = (int)$_POST['price'];
    $value = (int)$_POST['value'];
    $name = $con->real_escape_string($_POST['name']);
    $summary = $con->real_escape_string($_POST['summary']);
    $review = $con->real_escape_string($_POST['review']);
    $review_date = date("Y-m-d");

    // Handle image upload
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $imagePath = "uploads/" . $imageName;

        // Ensure the uploads directory exists
        if (!is_dir('uploads')) {
            mkdir('uploads', 0755, true);
        }

        // Move the uploaded image to the server's directory
        if (!move_uploaded_file($imageTmpName, $imagePath)) {
            echo "Failed to upload image.";
            exit;
        }
    }

    $sql = "INSERT INTO reviews (user_id, product_id, quality, price, value, name, summary, review, review_date, image)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('iiiissssss', $user_id, $product_id, $quality, $price, $value, $name, $summary, $review, $review_date, $imagePath);

    if ($stmt->execute()) {
        echo "<script>alert('New review added successfully')</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$con->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Review</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="number"], textarea, input[type="file"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
            font-size: 16px;
        }
        textarea {
            resize: vertical;
            height: 150px;
        }
        button {
            padding: 12px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .btn-back {
            background-color: #6c757d;
            margin-top: 10px;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
        @media (max-width: 600px) {
            .container {
                margin: 20px;
                padding: 15px;
            }
            button {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add Review</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <label for="user_id">User Id:</label>
        <input type="number" name="user_id" value="<?php echo $user_id; ?>" readonly>
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
        
        <label for="quality">Quality (1-5):</label>
        <input type="number" id="quality" name="quality" min="1" max="5" required>
        
        <label for="price">Price (1-5):</label>
        <input type="number" id="price" name="price" min="1" max="5" required>
        
        <label for="value">Value (1-5):</label>
        <input type="number" id="value" name="value" min="1" max="5" required>
        
        <label for="name">Your Name:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="summary">Summary:</label>
        <input type="text" id="summary" name="summary" required>
        
        <label for="review">Review:</label>
        <textarea id="review" name="review" required></textarea>
        
        <label for="image">Upload Image:</label>
        <input type="file" id="image" name="image" accept="image/*">
        
        <button type="submit">Submit Review</button>
    </form>
    <a href="laptop_view.php"><button class="btn-back">Back to Home</button></a>
</div>

</body>
</html>
