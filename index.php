<?php
session_start();
include 'db.php'; // Connect to database
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Event Booking System</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      font-family: "Segoe UI", Arial, sans-serif;
      background-color: #f7f7f7;
      margin: 0;
      padding: 0;
    }
    header {
      background-color: #34495e;
      color: white;
      padding: 20px;
      text-align: center;
    }
    nav {
      background: #2c3e50;
      padding: 10px;
      text-align: center;
    }
    nav a {
      color: white;
      margin: 0 15px;
      text-decoration: none;
      font-weight: bold;
    }
    nav a:hover {
      text-decoration: underline;
    }
    .container {
      width: 90%;
      max-width: 900px;
      margin: 40px auto;
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #333;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: center;
    }
    th {
      background-color: #2980b9;
      color: white;
    }
    tr:nth-child(even) {
      background-color: #f2f2f2;
    }
    .btn {
      background-color: #2980b9;
      color: white;
      padding: 8px 14px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
    }
    .btn:hover {
      background-color: #1f6391;
    }
  </style>
</head>
<body>

<header>
  <h1>🎟️ Event Booking System</h1>
  <p>Book your favorite events easily</p>
</header>

<nav>
  <?php if (isset($_SESSION['user_id'])): ?>
    <?php if ($_SESSION['role'] == 'admin'): ?>
      <a href="admin/dashboard.php">Admin Dashboard</a>
    <?php else: ?>
      <a href="user/dashboard.php">User Dashboard</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
  <?php else: ?>
    <a href="login.php">Login</a>
    <a href="register.php">Register</a>
  <?php endif; ?>
</nav>

<div class="container">
  <h2>Upcoming Events</h2>

  <?php
  // ✅ Correct query
  $result = $conn->query("SELECT * FROM events ORDER BY date ASC");

  if ($result && $result->num_rows > 0):
  ?>
  <table>
    <tr>
      <th>Event Name</th>
      <th>Date & Time</th>
      <th>Location</th>
      <th>Seats Left</th>
      <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['event_name']) ?></td>
      <td><?= htmlspecialchars($row['date']) ?></td>
      <td><?= htmlspecialchars($row['location']) ?></td>
      <td><?= htmlspecialchars($row['seats']) ?></td>
      <td>
        <?php if ($row['seats'] > 0): ?>
          <?php if (isset($_SESSION['user_id'])): ?>
            <a href="user/book_event.php?id=<?= $row['id'] ?>" class="btn">Book Now</a>
          <?php else: ?>
            <a href="login.php" class="btn">Login to Book</a>
          <?php endif; ?>
        <?php else: ?>
          <span style="color:red;">Sold Out</span>
        <?php endif; ?>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
  <?php else: ?>
    <p style="text-align:center;">No upcoming events available.</p>
  <?php endif; ?>
</div>

<footer style="text-align:center; padding:15px; background:#34495e; color:white;">
  &copy; <?= date('Y') ?> Event Booking System | All rights reserved.
</footer>

</body>
</html>
