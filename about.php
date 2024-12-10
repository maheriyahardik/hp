<?php include "menu.php"; ?>
<style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        margin: 0;
        padding: 0;
        background-color: #f9f9f9;
    }

    .container {
        width: 80%;
        margin: auto;
    }

    .about {
        padding: 50px 0;
    }

    .about .titlepage {
        text-align: left;
        padding-right: 30px;
    }

    .about h2 {
        font-size: 36px;
        color: #333;
        margin-bottom: 20px;
    }

    .about p {
        font-size: 18px;
        color: #666;
        line-height: 1.8;
        margin-bottom: 30px;
    }

    .about_img {
        text-align: right;
    }

    .about_img img {
        max-width: 100%;
        height: auto;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .read_more {
        display: inline-block;
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .read_more:hover {
        background-color: #0056b3;
    }

    .back-to-home {
        display: inline-block;
        margin-top: 10px;
        padding: 10px 20px;
        background-color: #28a745;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .back-to-home:hover {
        background-color: #218838;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .about .row.d_flex {
            flex-direction: column;
        }
        .about .col-md-5, .about .col-md-7 {
            width: 100%;
        }
        .about_img {
            text-align: center;
        }
    }
</style>

<!-- end header inner -->
<div class="about">
    <div class="container">
        <div class="row d_flex">
            <div class="col-md-5">
                <div class="titlepage">
                    <h2>About Us</h2>
                    <p>We are a technology company born of the belief that companies should do more than just make a profit. They should 
                        make the world a better place. Our efforts in climate action, human rights, and digital equity prove that we are
                        doing everything in our power to make it so. With over 80 years of actions that prove our intentions, we have the
                        confidence to envision a world where innovation drives extraordinary contributions to humanity. And our technology
                        - a product and service portfolio of personal systems, printers, and 3D printing solutions – was created to inspire
                        this meaningful progress. We know that thoughtful ideas can come from anyone, anywhere, at any time. And all it 
                        takes is one to change the world.</p>
                </div>
            </div>
            <div class="col-md-7">
                <div class="about_img">
                    <figure><img src="images/about.jpg" alt="About Us Image"/></figure>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end about section -->

<?php include "footer.php"; ?>
