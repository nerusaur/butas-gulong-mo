<?php
include 'connect.php';
session_start();


// Handle registration
if (isset($_POST['register'])) {
  // Validate form input to ensure all fields are filled
  if (!empty($_POST['newName']) && !empty($_POST['newEmail']) && !empty($_POST['newPassword'])) {
      $name = $_POST['newName'];
      $email = $_POST['newEmail'];
      $password = password_hash($_POST['newPassword'], PASSWORD_DEFAULT); // Hash the password

      // Check if the email already exists
      $check_email_sql = $conn->prepare("SELECT Email FROM account_table WHERE Email = ?");
      $check_email_sql->bind_param("s", $email);
      $check_email_sql->execute();
      $check_email_sql->store_result();

      if ($check_email_sql->num_rows > 0) {
          echo "<script>alert('Email is already registered. Please use a different email.');</script>";
      } else {
          // Use prepared statement to insert data into the database
          $register_sql = $conn->prepare("INSERT INTO account_table (Name, Email, Password) VALUES (?, ?, ?)");
          $register_sql->bind_param("sss", $name, $email, $password);

          // Check if the registration is successful
          if ($register_sql->execute()) {
              echo "<script>alert('Registration successful! Please login.');</script>";
          } else {
              echo "Error: " . $register_sql->error;
          }

          $register_sql->close();
      }

      $check_email_sql->close();
  } else {
      echo "<script>alert('All fields are required.');</script>";
  }
}




// Handle login
if (isset($_POST['login'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Fetch id, Name, and Password from the database
  $login_sql = $conn->prepare("SELECT id, Name, Password FROM account_table WHERE Email = ?");
  $login_sql->bind_param("s", $email);
  $login_sql->execute();
  $result = $login_sql->get_result();

  if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();

      if (password_verify($password, $row['Password'])) {
          // Store user details in session
          $_SESSION['user_id'] = $row['id'];
          $_SESSION['email'] = $email;
          $_SESSION['name'] = $row['Name']; // Store the Name in session

          header('Location: Home.php');
          exit();
      } else {
          echo "<script>alert('Invalid email or password.');</script>";
      }
  } else {
      echo "<script>alert('User not found.');</script>";
  }

  $login_sql->close();
}




$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login/Register</title>
  <link rel="stylesheet" href="index.css">
  <style>
    .hidden { display: none; }
    .active { background-color: #007bff; color: white; }
    .tabs button { padding: 10px; cursor: pointer; }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="tabs">
      <button id="loginButton" class="active" onclick="showForm('login')">Login</button>
      <button id="registerButton" onclick="showForm('register')">Register</button>
    </div>

    <!-- Login Form -->
    <form id="loginForm" action="index.php" method="post">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>

    <button type="submit" name="login">Login</button>
</form>


    <!-- Registration Form -->
    <form id="registerForm" action="index.php" method="post" class="hidden">
    <label for="newName">Name:</label>
    <input type="text" id="newName" name="newName" required>

    <label for="newEmail">Email:</label>
    <input type="email" id="newEmail" name="newEmail" required>

    <label for="newPassword">Password:</label>
    <input type="password" id="newPassword" name="newPassword" required>
    <button type="submit" name="register">Register</button>
</form>




  </div>

  <script>
    function showForm(type) {
      const loginForm = document.getElementById('loginForm');
      const registerForm = document.getElementById('registerForm');
      const loginButton = document.getElementById('loginButton');
      const registerButton = document.getElementById('registerButton');

      if (type === 'login') {
        loginForm.classList.remove('hidden');
        registerForm.classList.add('hidden');
        loginButton.classList.add('active');
        registerButton.classList.remove('active');
      } else {
        loginForm.classList.add('hidden');
        registerForm.classList.remove('hidden');
        loginButton.classList.remove('active');
        registerButton.classList.add('active');
      }
    }

    // Default to showing the login form
    showForm('login');
  </script>
</body>
</html>
