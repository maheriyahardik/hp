<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- basic -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- mobile metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <!-- site metas -->
    <title>HP</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- bootstrap css -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- style css -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Responsive-->
    <link rel="stylesheet" href="css/responsive.css">
    <!-- fevicon -->
    <link rel="icon" href="images/fevicon.png" type="image/gif" />
    <!-- Scrollbar Custom CSS -->
    <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">
    <!-- Tweaks for older IEs-->
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
</head>
<body class="main-layout inner_posituong computer_page">
    <header>
        <!-- header inner -->
        <div class="header">
            <div class="container-fluid">
                <div class="row">

                <div class="col-xl-2 col-lg-3 col-md-3 col-sm-3 logo_section">
                        <div class="full">
                            <div class="center-desk">
                                <div class="logo">
                                    <a href="index.php"><img src="images/logo.png" alt="Logo" /></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <nav class="navigation navbar navbar-expand-md navbar-dark">
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample04" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarsExample04">
                                <ul class="navbar-nav mr-auto">
                                    <li class="nav-item">
                                        <a class="nav-link" href="index.php">Home</a>
                                    </li>
                                    <!-- <li class="nav-item">
                                        <a class="nav-link" href="about.php">About</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="computer.php">Computer</a>
                                    </li>-->
                                    <li class="nav-item">
                                        <a class="nav-link" href="laptop_view.php">Products</a>
                                    </li>
                                   <li class="nav-item">
                                        <a class="nav-link" href="my_cart.php">Cart</a>
                                    </li> 
                                    <li class="nav-item">
                                        <a class="nav-link" href="my_wishlist.php">My Wishlist</a>
                                    </li>   
                                    <!--<li class="nav-item">
                                        <a class="nav-link" href="contact.php">Contact Us</a>
                                    </li>-->
                                    <li class="nav-item">
                                        <a class="nav-link" href="about.php">About</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="contact.php">Contact</a>
                                    <li> <li class="nav-item">
                                        <a class="nav-link" href="view_orders.php">Order </a>
                                    </li> 
                                    <!-- <li class="nav-item">
                                        <a class="nav-link" href="place_order.php">place order </a>
                                    <li> -->
                                    <!-- </li> <li class="nav-item">
                                        <a class="nav-link" href="checkout.php">checkout Product</a>
                                    </li> -->
                                    <?php if(isset($_SESSION['user']) || isset($_SESSION['admin'])) { ?>
                                        <li class="nav-item">
                                            <a class="nav-link" href="logout.php">Logout</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="User_data.php">Profile</a>
                                        </li>
                                    <?php } else { ?>
                                        <li class="nav-item">
                                            <a class="nav-link" href="login.php">Login</a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- end header inner -->
    <!-- end header -->
    <section class="banner_main">
        <div id="banner1" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <li data-target="#banner1" data-slide-to="0" class="active"></li>
                <li data-target="#banner1" data-slide-to="1"></li>
                <li data-target="#banner1" data-slide-to="2"></li>
                <li data-target="#banner1" data-slide-to="3"></li>
                <li data-target="#banner1" data-slide-to="4"></li>
            </ol>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="container">
                        <div class="carousel-caption">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="text-bg">
                                    <span>hp</span>
                                    <span>Products<span>
                                        <span>and<span>
                                        <span>Accessories</spen>
                                        <p>High-performance laptops for gaming, programming, and everyday use, equipped with the latest processors and ample 
                                        storage.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text_img">
                                        <figure><img src="images/pct.png" alt="Product Image"/></figure>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item" style="padding:0px;">
                    <div class="container">
                        <div class="carousel-caption">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="text-bg">
                                    <span>hp</span>
                                    <span>Products<span>
                                        <span>and<span>
                                        <span>Accessories</spen>
                                        <p>High-performance laptops for gaming, programming, and everyday use, equipped with the latest processors and ample 
                                        storage.</p>
                                         </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text_img">
                                        <figure><img src="images/pct1.jpg" alt="Product Image"/></figure>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="container">
                        <div class="carousel-caption">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="text-bg">
                                    <span>hp</span>
                                    <span>Products<span>
                                        <span>and<span>
                                        <span>Accessories</spen>
                                        <p>High-performance laptops for gaming, programming, and everyday use, equipped with the latest processors and ample 
                                        storage.</p>
                                         </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text_img">
                                        <figure><img src="images/pct2.png" alt="Product Image"/></figure>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="container">
                        <div class="carousel-caption">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="text-bg">
                                    <span>hp</span>
                                    <span>Products<span>
                                        <span>and<span>
                                        <span>Accessories</spen>
                                        <p>High-performance laptops for gaming, programming, and everyday use, equipped with the latest processors and ample 
                                        storage.</p>
                                          </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text_img">
                                        <figure><img src="images/pct3.png" alt="Product Image"/></figure>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="container">
                        <div class="carousel-caption">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="text-bg">
                                    <span>hp</span>
                                    <span>Products<span>
                                        <span>and<span>
                                        <span>Accessories</spen>
                                        <p>High-performance laptops for gaming, programming, and everyday use, equipped with the latest processors and ample 
                                        storage.</p>
                                            </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text_img">
                                        <figure><img src="images/pct4.png" alt="Product Image"/></figure>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <a class="carousel-control-prev" href="#banner1" role="button" data-slide="prev">
                <i class="fa fa-chevron-left" aria-hidden="true"></i>
            </a>
            <a class="carousel-control-next" href="#banner1" role="button" data-slide="next">
                <i class="fa fa-chevron-right" aria-hidden="true"></i>
            </a>
        </div>
    </section>
</body>
</html>
