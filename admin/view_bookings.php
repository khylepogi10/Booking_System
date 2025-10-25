<?php
session_start();
include '../db.php';
if ($_SESSION['role'] != 'admin') { header("Location: ../login.php"); exit; }

$result = $conn->query("SELECT b.id, u.name, e.title, b.booked_on 
                        FROM bookings b 
                        JOIN users u ON b.user_id = u.id 
                        JOIN events e ON b.event_id = e.id");
?>
<!DOCTYPE html>
<html>
<head><title>View Bookings</title></head>
<body>
<h2>All Bookings</h2>
<a href="dashboard.php">Back</a>
<hr>
<table border="1" cellpadding="8">
<tr><th>User</th><th>Event</th><th>Booked On</th></tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['name'] ?></td>
<td><?= $row['title'] ?></td>
<td><?= $row['booked_on'] ?></td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
