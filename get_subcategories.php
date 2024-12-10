<?php
session_start();
include "connection.php";

if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];

    // Query to fetch subcategories for the selected category
    $query = "SELECT id, name FROM subcategories WHERE category_id = '$category_id'";
    $result = mysqli_query($con, $query);

    $subcategories = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $subcategories[] = $row;
    }

    // Return subcategories as JSON
    echo json_encode($subcategories);
} else {
    // Handle error if category_id parameter is not provided
    echo json_encode(array('error' => 'Category ID parameter missing'));
}
?>
