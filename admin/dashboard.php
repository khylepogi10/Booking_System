<?php
session_start();
include '../db.php';
if ($_SESSION['role'] != 'admin') { header("Location: ../login.php"); exit; }

$events = $conn->query("SELECT * FROM events");
?>
<!DOCTYPE html>
<html>
<head><title>Admin Dashboard</title></head>
<body>
<h2>Admin Dashboard</h2>
<a href="add_event.php">Add New Event</a> |
<a href="view_bookings.php">View Bookings</a> |
<a href="../logout.php">Logout</a>
<hr>
<table border="1" cellpadding="8">
<tr><th>Title</th><th>Date</th><th>Venue</th><th>Seats</th><th>Action</th></tr>
<?php while($row = $events->fetch_assoc()): ?>
<tr>
<td><?= $row['title'] ?></td>
<td><?= $row['date'] ?></td>
<td><?= $row['venue'] ?></td>
<td><?= $row['seats'] ?></td>
<td>
<a href="edit_event.php?id=<?= $row['id'] ?>">Edit</a> |
<a href="delete_event.php?id=<?= $row['id'] ?>">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
