<?php
session_start();
include 'db.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Secure login query
    $stmt = $conn->prepare("SELECT * FROM tbl_user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit;
    } else {
        echo "<script>alert('Invalid email or password');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | Event Booking System</title>
  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #2c3e50, #2980b9);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .login-container {
      background: #fff;
      width: 360px;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
      text-align: center;
    }
    h2 {
      margin-bottom: 20px;
      color: #2c3e50;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    input[type="email"], input[type="password"] {
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      transition: all 0.3s ease;
    }
    input:focus {
      border-color: #2980b9;
      box-shadow: 0 0 4px rgba(41, 128, 185, 0.4);
      outline: none;
    }
    button {
      background-color: #2980b9;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }
    button:hover {
      background-color: #1f6391;
    }
    a {
      display: block;
      margin-top: 15px;
      text-decoration: none;
      color: #2980b9;
      font-size: 14px;
      transition: 0.3s;
    }
    a:hover {
      color: #1f6391;
      text-decoration: underline;
    }
    .footer {
      margin-top: 20px;
      color: #888;
      font-size: 12px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>🎟️ Login</h2>
    <form method="POST">
      <input type="email" name="email" placeholder="Enter your email" required>
      <input type="password" name="password" placeholder="Enter your password" required>
      <button type="submit" name="login">Login</button>
    </form>
    <a href="register.php">Create an account</a>
    <div class="footer">&copy; <?= date('Y') ?> Event Booking System</div>
  </div>
</body>
</html>
