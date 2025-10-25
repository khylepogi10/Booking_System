<?php
session_start();
include '../db.php';
if ($_SESSION['role'] != 'admin') { header("Location: ../login.php"); exit; }

if (isset($_POST['add'])) {
    $title = $_POST['title'];
    $desc = $_POST['desc'];
    $date = $_POST['date'];
    $venue = $_POST['venue'];
    $seats = $_POST['seats'];

    $stmt = $conn->prepare("INSERT INTO events (title, description, date, venue, seats) VALUES (?,?,?,?,?)");
    $stmt->bind_param("ssssi", $title, $desc, $date, $venue, $seats);
    $stmt->execute();
    echo "<script>alert('Event added successfully'); window.location='dashboard.php';</script>";
}
?>
<!DOCTYPE html>
<html>
<head><title>Add Event</title></head>
<body>
<h2>Add Event</h2>
<form method="POST">
Title: <input type="text" name="title" required><br><br>
Description: <textarea name="desc"></textarea><br><br>
Date: <input type="date" name="date" required><br><br>
Venue: <input type="text" name="venue" required><br><br>
Seats: <input type="number" name="seats" required><br><br>
<button type="submit" name="add">Add Event</button>
</form>
<a href="dashboard.php">Back</a>
</body>
</html>
