<?php
session_start();
include(__DIR__ . '/../db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Fetch events
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
        body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #e3f2fd, #ffffff);
    margin: 0;
    padding: 20px;
    color: #2c3e50;
}

h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 10px;
}

a {
    color: #3498db;
    text-decoration: none;
    margin-right: 15px;
    font-weight: 500;
    transition: 0.3s;
}

a:hover {
    text-decoration: underline;
    color: #21618c;
}

hr {
    border: 0;
    border-top: 2px solid #d6eaf8;
    margin: 20px 0;
}

/* Table container */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    background-color: white;
}

/* Table headers */
th {
    background-color: #2980b9;
    color: white;
    padding: 12px;
    text-align: left;
    letter-spacing: 0.5px;
}

/* Table data */
td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    vertical-align: middle;
}

/* Alternate row colors */
tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* Hover row effect */
tr:hover {
    background-color: #e8f4fd;
    transition: background 0.2s ease;
}

/* Book link button style */
td a {
    display: inline-block;
    background-color: #27ae60;
    color: white;
    padding: 6px 12px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
    transition: 0.3s;
}

td a:hover {
    background-color: #219150;
    transform: scale(1.05);
}

/* Sold out label */
td span {
    color: #e74c3c;
    font-weight: bold;
}

/* Responsive table (for mobile) */
@media (max-width: 768px) {
    body {
        padding: 10px;
    }

    table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }

    th, td {
        font-size: 14px;
    }
}

    </style>
</head>
<body>

<h2>Welcome to Event Booking</h2>

<a href="my_bookings.php">My Bookings</a>
<a href="../logout.php">Logout</a>

<hr>

<h3>Available Events</h3>
<table>
    <tr>
        <th>Title</th>
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
                <a href="my_bookings.php?id=<?= $row['id'] ?>">Book</a>
            <?php else: ?>
                <span style="color: red;">Sold Out</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
