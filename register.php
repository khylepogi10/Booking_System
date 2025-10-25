<?php
include(__DIR__ . '/db.php'); // Corrected path

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Email already exists.');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Registration failed.');</script>";
        }

        $stmt->close();
    }

    $check->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register | Event Booking System</title>
  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #2980b9, #6dd5fa);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .register-container {
      background: #fff;
      width: 380px;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      transition: all 0.3s ease;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus {
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

  <div class="register-container">
    <h2>📝 Create Account</h2>
    <form method="POST">
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email Address" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="register">Register</button>
    </form>
    <a href="login.php">Already have an account? Login</a>
    <div class="footer">&copy; <?= date('Y') ?> Event Booking System</div>
  </div>

</body>
</html>
