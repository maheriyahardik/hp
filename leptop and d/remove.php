<!DOCTYPE html>
<html lang="en">
<body>
   <?php include "menu.php";?>
   <!-- header -->
   <?php include "header.php"; ?>
    <div class="container mt-5">
        <h2>Remove from Cart</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="cart-items">
                <!-- Cart items will be inserted here dynamically -->
            </tbody>
        </table>
    </div>
   <!-- footer -->
   <?php include "footer.php"; ?>
   <!-- end footer -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/remove.js"></script>
</body>
</html>
