<?php
include "connection.php";

if (isset($_GET['category_id'])) {
    $category_id = mysqli_real_escape_string($con, $_GET['category_id']);
    $query = "SELECT * FROM subcategories WHERE category_id='$category_id'";
    $result = mysqli_query($con, $query);
    $subcategories = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $subcategories[] = $row;
    }
    echo json_encode($subcategories);
}
?>
