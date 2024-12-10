<!DOCTYPE html>
<html>
<head>
    <style>
        .three_box {
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        .row {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .col-md-4 {
            flex: 1;
            max-width: 32%;
            box-sizing: border-box;
            margin-bottom: 20px;
        }

        .box_text {
            background: #f5f5f5;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .box_text img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .box_text h1 {
            font-size: 24px;
            color: #333;
            margin: 20px 0;
        }

        .box_text p {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }

        .view-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .view-button:hover {
            background-color: #0056b3;
        }

        .back-to-home {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            margin: 20px auto;
            text-align: center;
        }

        .back-to-home:hover {
            background-color: #218838;
        }

        .back-to-home-container {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="three_box m-4">
        <div class="container">
            <div class="row">
                <?php
                    $qproduct = "SELECT * FROM products ORDER BY RAND() LIMIT 3";
                    $rsProduct = mysqli_query($con, $qproduct);
                    while ($row = mysqli_fetch_array($rsProduct)) {
                ?>
                <div class="col-md-4">
                    <div class="box_text" style="height:650px;display:flex;justify-content:center;align-items:center;flex-direction:column;">
                        <i><img src="<?php echo $row['image']; ?>" alt="#"/></i>
                        <h1><?php echo $row['name']; ?></h1>
                        <!-- <p><?php echo $row['description']; ?></p> -->
                        <p><?php echo $row['price']; ?></p>
                        <a href='product_details.php?product_id=<?php echo $row["id"]; ?>' class='view-button'>View Details</a>
                    </div>
                </div>
                <?php } ?>
            </div>
            <div class="back-to-home-container">
                <a href="laptop_view.php" class="back-to-home">view all products</a>
            </div>
        </div>
    </div>
</body>
</html>
