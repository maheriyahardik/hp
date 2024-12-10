<?php
// Database connection
include "connection.php";

// Start session and get user_id
session_start();
$user_id = isset($_SESSION['user']) ? $_SESSION['user']['id'] : 0; // Ensure user is set in the session
if ($user_id === 0) {
   // Redirect to login page if user is not logged in
   header("Location: login.php");
   exit;
}

// Initialize success and error messages
$message = '';
$success = false;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $name = trim($_POST['Name']);
   $email = trim($_POST['Email']);
   $phone = trim($_POST['Phone_Number']);
   $messageContent = trim($_POST['Message']);

   // Validate inputs
   $errors = [];
   if (empty($name)) {
      $errors[] = "Name is required.";
   }
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "Invalid email format.";
   }
   if (!preg_match("/^[0-9]{10}$/", $phone)) {
      $errors[] = "Invalid phone number.";
   }
   if (empty($messageContent)) {
      $errors[] = "Message is required.";
   }

   if (empty($errors)) {
      // Prepare and execute the SQL statement
      $stmt = $con->prepare("INSERT INTO contacts (name, email, phone_number, message, user_id) VALUES (?, ?, ?, ?, ?)");
      $stmt->bind_param("ssssi", $name, $email, $phone, $messageContent, $user_id);
     
      if ($stmt->execute()) {
         $success = true;
         $message = "Message sent successfully!";
      } else {
         $message = "Error: " . $stmt->error;
      }
      $stmt->close();
   } else {
      $message = implode('<br>', $errors);
   }

   $con->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Contact Form</title>
   <style>
      /* General styles for body and main layout */
      body {
         font-family: Arial, sans-serif;
         background-color: #f4f7f6;
         margin: 0;
         padding: 0;
         color: #333;
      }

      .container {
         max-width: 800px;
         margin: 40px auto;
         background-color: #fff;
         padding: 20px;
         border-radius: 8px;
         box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      }

      .titlepage h2 {
         text-align: center;
         color: #007bff;
         font-size: 32px;
         margin-bottom: 20px;
      }

      .main_form {
         display: flex;
         flex-direction: column;
         gap: 15px;
      }

      .main_form input[type="text"],
      .main_form input[type="email"],
      .main_form textarea {
         width: 100%;
         padding: 15px;
         border: 1px solid #ddd;
         border-radius: 5px;
         box-sizing: border-box;
         font-size: 16px;
         transition: border-color 0.3s ease;
      }

      .main_form input[type="text"]:focus,
      .main_form input[type="email"]:focus,
      .main_form textarea:focus {
         border-color: #007bff;
         outline: none;
      }

      .main_form textarea {
         height: 150px;
         resize: vertical;
      }

      .send_btn {
         background-color: #007bff;
         color: #fff;
         padding: 15px;
         border: none;
         border-radius: 5px;
         cursor: pointer;
         font-size: 18px;
         transition: background-color 0.3s ease;
      }

      .send_btn:hover {
         background-color: #0056b3;
      }

      .send_btn:disabled {
         background-color: #ccc;
         cursor: not-allowed;
      }

      .back-to-home {
         display: inline-block;
         margin-top: 15px;
         color: #007bff;
         text-decoration: none;
         font-size: 16px;
         transition: color 0.3s ease;
      }

      .back-to-home:hover {
         color: #0056b3;
      }

      .error {
         color: #d9534f;
         margin-top: 10px;
         font-size: 16px;
         border: 1px solid #d9534f;
         padding: 10px;
         border-radius: 5px;
         background-color: #f9d6d5;
      }
      .success {
         color: #5bc0de;
         margin-top: 10px;
         font-size: 16px;
         border: 1px solid #5bc0de;
         padding: 10px;
         border-radius: 5px;
         background-color: #d9edf7;
      }

      /* Responsive design */
      @media (max-width: 768px) {
         .container {
            padding: 15px;
         }

         .titlepage h2 {
            font-size: 28px;
         }
      }
   </style>
</head>
<body>

<div class="container">
   <div class="titlepage">
      <h2>Contact Us</h2>
   </div>

   <?php if (!empty($message)): ?>
      <p class="<?php echo $success ? 'success' : 'error'; ?>">
         <?php echo $message; ?>
      </p>
   <?php endif; ?>

   <form id="contactForm" class="main_form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
      <input placeholder="Name" type="text" name="Name" required> 
      <input placeholder="Email" type="email" name="Email" required> 
      <input placeholder="Phone Number" type="text" name="Phone_Number" required>                          
      <textarea placeholder="Message" name="Message" required></textarea>
      <button class="send_btn" type="submit">Send</button>
   </form>

   <a href="index.php" class="back-to-home">Back to Home</a>
</div>

<!-- JavaScript files -->
<script src="js/jquery.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/jquery-3.0.0.min.js"></script>
<script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="js/custom.js"></script>

<script>
document.getElementById('contactForm').addEventListener('submit', function(event) {
   let isValid = true;
   let errors = [];

   const name = document.getElementsByName('Name')[0].value.trim();
   const email = document.getElementsByName('Email')[0].value.trim();
   const phone = document.getElementsByName('Phone_Number')[0].value.trim();
   const message = document.getElementsByName('Message')[0].value.trim();

   // Validate name
   if (name === '') {
      isValid = false;
      errors.push('Name is required.');
   }

   // Validate email
   const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
   if (!emailPattern.test(email)) {
      isValid = false;
      errors.push('Invalid email format.');
   }

   // Validate phone number (10 digits)
   const phonePattern = /^[0-9]{10}$/;
   if (!phonePattern.test(phone)) {
      isValid = false;
      errors.push('Invalid phone number.');
   }

   // Validate message
   if (message === '') {
      isValid = false;
      errors.push('Message is required.');
   }

   // Display errors if any
   const errorContainer = document.createElement('div');
   errorContainer.className = 'error';
   errors.forEach(function(error) {
      const errorElem = document.createElement('p');
      errorElem.textContent = error;
      errorContainer.appendChild(errorElem);
   });

   if (!isValid) {
      event.preventDefault();
      // Remove existing errors and append new ones
      const form = document.getElementById('contactForm');
      const existingErrorContainer = form.querySelector('.error');
      if (existingErrorContainer) {
         existingErrorContainer.remove();
      }
      form.prepend(errorContainer);
   }
});
</script>

</body>
</html>
