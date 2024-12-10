<!DOCTYPE html>
<html lang="en">
<!-- body -->
<body class="main-layout inner_posituong computer_page">
    <!-- loader  -->
    <!-- <div class="loader_bg">
    <div class="loader"><img src="images/loading.gif" alt="#" /></div>
    </div> -->
    <!-- end loader -->
    <?php include "menu.php";?>
    <!-- header -->
    <?php include "header.php"; ?>
    <!-- Products Section -->
    <div class="container mt-5">
        <h2>All Products</h2>
        <div class="row">
            <!-- Product Item -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <img src="images/computer1.jpg" class="card-img-top" alt="Product Image">
                    <div class="card-body">
                        <h5 class="card-title">Essentials Home</h5>
                        <p class="card-text">Starting from: ₹39,499</p>
                        <a href="addcart.html" class="btn btn-primary">Shop Now</a>
                        <a href="addcart.html" class="btn btn-secondary">Add to Cart</a>
                        <a href="addcart.html" class="btn btn-danger">Remove</a>
                    </div>
                </div>
            </div>
            <!-- Repeat for each product -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <img src="images/computer2.jpg" class="card-img-top" alt="Product Image">
                    <div class="card-body">
                        <h5 class="card-title">Pavilion</h5>
                        <p class="card-text">Starting from: ₹109,199</p>
                        <a href="addcart.html" class="btn btn-primary">Shop Now</a>
                        <a href="addcart.html" class="btn btn-secondary">Add to Cart</a>
                        <a href="addcart.html" class="btn btn-danger">Remove</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <img src="images/computer3.jpg" class="card-img-top" alt="Product Image">
                    <div class="card-body">
                        <h5 class="card-title">OMEN Gaming</h5>
                        <p class="card-text">Description</p>
                        <a href="addcart.html" class="btn btn-primary">Shop Now</a>
                        <a href="addcart.html" class="btn btn-secondary">Add to Cart</a>
                        <a href="addcart.html" class="btn btn-danger">Remove</a>
                    </div>
                </div>
            </div>
            <!-- Add more products as needed -->
        </div>
    </div>

    <!-- Footer -->
    <!-- footer -->
   <?php include "footer.php"; ?>
   <!-- end footer -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/cart1.js"></script>

</html>
