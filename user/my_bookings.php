<?php
session_start();
include(__DIR__ . '/../db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $booking_id = intval($_GET['cancel']);

    $check_booking = $conn->prepare("SELECT event_id FROM bookings WHERE id = ? AND user_id = ?");
    $check_booking->bind_param("ii", $booking_id, $user_id);
    $check_booking->execute();
    $check_booking->store_result();

    if ($check_booking->num_rows > 0) {
        $check_booking->bind_result($event_id);
        $check_booking->fetch();

        $delete_booking = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        $delete_booking->bind_param("i", $booking_id);
        $delete_booking->execute();

        $update_seats = $conn->prepare("UPDATE events SET seats = seats + 1 WHERE id = ?");
        $update_seats->bind_param("i", $event_id);
        $update_seats->execute();

        echo "<script>alert('Booking cancelled successfully!'); window.location='my_bookings.php';</script>";

        $delete_booking->close();
        $update_seats->close();
    } else {
        echo "<script>alert('Booking not found.'); window.location='my_bookings.php';</script>";
    }

    $check_booking->close();
}

$stmt = $conn->prepare("
    SELECT b.id, e.event_name, e.date, e.location, e.price, b.booking_date, e.image
    FROM bookings b
    JOIN events e ON b.event_id = e.id
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
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
        .bookings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
        .booking-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }
        .booking-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .no-image {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            font-weight: 600;
        }
        .booking-content {
            padding: 20px;
        }
        .event-title {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 15px;
            font-weight: 700;
        }
        .booking-info {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .booking-info strong {
            min-width: 100px;
        }
        .price {
            font-size: 22px;
            color: #27ae60;
            font-weight: bold;
            margin: 15px 0;
        }
        .btn-cancel {
            background-color: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            width: 100%;
            text-align: center;
            margin-top: 10px;
        }
        .btn-cancel:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }
        .no-bookings {
            background: white;
            padding: 60px 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .no-bookings h2 {
            color: #7f8c8d;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #2980b9;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #1f6391;
            transform: translateY(-2px);
        }
        .total-spent {
            background: #27ae60;
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .total-spent h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .total-spent .amount {
            font-size: 32px;
            font-weight: bold;
        }
        @media (max-width: 768px) {
            .bookings-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>My Bookings</h1>
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php
        $total_spent = 0;
        $bookings = [];
        while ($row = $result->fetch_assoc()) {
            $total_spent += $row['price'];
            $bookings[] = $row;
        }
        ?>

        <div class="total-spent">
            <h2>Total Amount Spent</h2>
            <div class="amount">$<?= number_format($total_spent, 2) ?></div>
        </div>

        <div class="bookings-grid">
            <?php foreach ($bookings as $booking): ?>
            <div class="booking-card">
                <?php if ($booking['image']): ?>
                    <img src="../uploads/<?= htmlspecialchars($booking['image']) ?>" alt="<?= htmlspecialchars($booking['event_name']) ?>" class="booking-image">
                <?php else: ?>
                    <div class="no-image"><?= htmlspecialchars(substr($booking['event_name'], 0, 1)) ?></div>
                <?php endif; ?>

                <div class="booking-content">
                    <div class="event-title"><?= htmlspecialchars($booking['event_name']) ?></div>

                    <div class="booking-info">
                        <strong>Event Date:</strong>
                        <?= htmlspecialchars($booking['date']) ?>
                    </div>

                    <div class="booking-info">
                        <strong>Location:</strong>
                        <?= htmlspecialchars($booking['location']) ?>
                    </div>

                    <div class="booking-info">
                        <strong>Booked On:</strong>
                        <?= htmlspecialchars($booking['booking_date']) ?>
                    </div>

                    <div class="price">$<?= number_format($booking['price'], 2) ?></div>

                    <a href="my_bookings.php?cancel=<?= $booking['id'] ?>"
                       class="btn-cancel"
                       onclick="return confirm('Are you sure you want to cancel this booking?')">
                        Cancel Booking
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-bookings">
            <h2>You haven't booked any events yet</h2>
            <p style="color: #95a5a6; margin-bottom: 30px;">Browse our exciting events and book your first one!</p>
            <a href="dashboard.php" class="btn-primary">Browse Events</a>
        </div>
    <?php endif; ?>
</body>
</html>
