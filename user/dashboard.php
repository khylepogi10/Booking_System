<?php
session_start();
include(__DIR__ . '/../db.php');

// Auto logout after 15 minutes of inactivity (optional)
$timeout = 900;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit;
}
$_SESSION['last_activity'] = time();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$events = $conn->prepare("SELECT id, event_name, date, location, seats, description FROM events ORDER BY date ASC");
$events->execute();
$result = $events->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        h2 { color: #2c3e50; }
        a { margin-right: 15px; color: #2980b9; text-decoration: none; }
        a:hover { text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        th { background-color: #2980b9; color: white; }
        tr:nth-child(even) { background-color: #eaf2f8; }
    </style>
</head>
<body>

<h2>Welcome to Event Booking</h2>
<a href="my_bookings.php">My Bookings</a>
<a href="logout.php">Logout</a> <!-- ✅ Fixed path -->

<hr>

<h3>Available Events</h3>
<table>
    <tr>
        <th>Name</th>
        <th>Date</th>
        <th>Venue</th>
        <th>Seats</th>
        <th>Description</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['event_name']) ?></td>
        <td><?= htmlspecialchars($row['date']) ?></td>
        <td><?= htmlspecialchars($row['location']) ?></td>
        <td><?= htmlspecialchars($row['seats']) ?></td>
        <td><?= htmlspecialchars($row['description']) ?></td>
        <td>
            <?php if ($row['seats'] > 0): ?>
                <a href="book_event.php?id=<?= $row['id'] ?>">Book</a>
            <?php else: ?>
                <span style="color: red;">Sold Out</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
