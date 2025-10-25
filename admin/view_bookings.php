<?php
session_start();
include '../db.php';
if ($_SESSION['role'] != 'admin') { header("Location: ../login.php"); exit; }

$timeout = 900;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit;
}
$_SESSION['last_activity'] = time();

$result = $conn->query("SELECT b.id, u.name, u.email, e.event_name, e.date, e.location, e.price, b.booking_date
                        FROM bookings b
                        JOIN users u ON b.user_id = u.id
                        JOIN events e ON b.event_id = e.id
                        ORDER BY b.booking_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .header {
            background: white;
            padding: 20px 40px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .header h1 {
            color: #2c3e50;
            font-size: 28px;
        }
        .back-link {
            color: #2980b9;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        .back-link:hover {
            color: #1f6391;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }
        th {
            background-color: #34495e;
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background-color: #f8f9fa;
        }
        .total-revenue {
            background: #27ae60;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }
        @media (max-width: 768px) {
            table {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>All Bookings</h1>
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>

    <div class="container">
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Event</th>
                        <th>Event Date</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Booked On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_revenue = 0;
                    while($row = $result->fetch_assoc()):
                        $total_revenue += $row['price'];
                    ?>
                    <tr>
                        <td>#<?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td>$<?= number_format($row['price'], 2) ?></td>
                        <td><?= htmlspecialchars($row['booking_date']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div class="total-revenue">
                Total Revenue: $<?= number_format($total_revenue, 2) ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #7f8c8d; font-size: 18px; padding: 40px;">No bookings found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
