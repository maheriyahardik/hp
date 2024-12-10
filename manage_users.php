<?php
session_start();
include "connection.php";

// Pagination variables
$entries_per_page = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $entries_per_page;

// Fetch users from database
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM users";
if (!empty($search)) {
    if (is_numeric($search)) {
        $query .= " WHERE id = $search";
    } else {
        $query .= " WHERE username LIKE '%$search%' OR name LIKE '%$search%' OR email LIKE '%$search%' OR mobile_number LIKE '%$search%'";
    }
}
$query .= " LIMIT $start, $entries_per_page";

$result = mysqli_query($con, $query);

// Count total users (for pagination)
$count_query = "SELECT COUNT(*) AS total_count FROM users";
if (!empty($search)) {
    if (is_numeric($search)) {
        $count_query .= " WHERE id = $search";
    } else {
        $count_query .= " WHERE username LIKE '%$search%' OR name LIKE '%$search%' OR email LIKE '%$search%' OR mobile_number LIKE '%$search%'";
    }
}

$count_result = mysqli_query($con, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_users = $count_row['total_count'];

// Delete user
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM users WHERE id = $delete_id";
    mysqli_query($con, $delete_query);
    header("Location: manage_users.php?page=$page&search=$search");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #007bff;
            margin-bottom: 20px;
            text-align: center;
        }

        form {
            margin-bottom: 20px;
            text-align: center;
        }

        form input[type="text"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            margin-right: 10px;
            width: 200px;
        }

        form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        form button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            padding: 8px 16px;
            margin: 0 5px;
            text-decoration: none;
            background-color: #e9ecef;
            color: #007bff;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 16px;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
        }

        .pagination a:hover:not(.active) {
            background-color: #dee2e6;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            text-align: center;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        .delete-btn {
            padding: 5px 10px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>All User List</h2>

    <!-- Search form -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
        Search by ID: <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <!-- User table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile Number</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['mobile_number']; ?></td>
                    <td><a href="manage_users.php?delete_id=<?php echo $row['id']; ?>&page=<?php echo $page; ?>&search=<?php echo $search; ?>" class="delete-btn">Delete</a></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if ($total_users > $entries_per_page) { ?>
        <div class="pagination">
            <?php
            $total_pages = ceil($total_users / $entries_per_page);
            $prev = $page - 1;
            $next = $page + 1;

            if ($page > 1) {
                echo "<a href='manage_users.php?page=$prev&search=$search'>Previous</a>";
            }

            for ($i = 1; $i <= $total_pages; $i++) {
                $active = ($page == $i) ? "active" : "";
                echo "<a href='manage_users.php?page=$i&search=$search' class='$active'>$i</a>";
            }

            if ($page < $total_pages) {
                echo "<a href='manage_users.php?page=$next&search=$search'>Next</a>";
            }
            ?>
        </div>
     <div>   </div>
     <h1></h1>
    <?php } ?>
    <a href="admin-home.php" class="back-btn">Back to Home Page</a>
    <a href="admin_view_addresses.php" class="back-btn">view users addres</a>
    <a href="admins_columns.php" class="back-btn">view admin</a>
    <a href="admin_cart.php" class="back-btn">view cart</a>
    <a href="admin_wishlist.php" class="back-btn">view wishlist</a>
    <a href="admin_contacts.php" class="back-btn">view contacts</a>

</div>

</body>
</html>
