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

if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    
    $sql = "SELECT id, name FROM subcategories WHERE category_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $options = "<option value=''>Select Subcategory</option>";
    while ($row = $result->fetch_assoc()) {
        $options .= "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
    }
    echo $options;
    
    $stmt->close();
}

$con->close();
?>
