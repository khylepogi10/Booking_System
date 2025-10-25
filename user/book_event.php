<?php
session_start();
include(__DIR__ . '/../db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $event_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    $event_check = $conn->prepare("SELECT event_name, date, location, price, seats FROM events WHERE id = ? AND seats > 0");
    $event_check->bind_param("i", $event_id);
    $event_check->execute();
    $event_check->store_result();

    if ($event_check->num_rows > 0) {
        $event_check->bind_result($event_name, $event_date, $event_location, $event_price, $seats);
        $event_check->fetch();

        $duplicate_check = $conn->prepare("SELECT id FROM bookings WHERE user_id = ? AND event_id = ?");
        $duplicate_check->bind_param("ii", $user_id, $event_id);
        $duplicate_check->execute();
        $duplicate_check->store_result();

        if ($duplicate_check->num_rows > 0) {
            echo "<script>
                alert('You have already booked this event!');
                window.location='dashboard.php';
            </script>";
            exit;
        }

        $insert = $conn->prepare("INSERT INTO bookings (user_id, event_id) VALUES (?, ?)");
        $insert->bind_param("ii", $user_id, $event_id);
        $insert->execute();

        $update = $conn->prepare("UPDATE events SET seats = seats - 1 WHERE id = ?");
        $update->bind_param("i", $event_id);
        $update->execute();

        echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Booking Confirmed</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .confirmation-card {
            background: white;
            max-width: 500px;
            width: 100%;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            text-align: center;
        }
        .success-icon {
            font-size: 60px;
            color: #27ae60;
            margin-bottom: 20px;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 28px;
        }
        .event-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            text-align: left;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ecf0f1;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #7f8c8d;
        }
        .detail-value {
            color: #2c3e50;
            font-weight: 600;
        }
        .price-total {
            font-size: 24px;
            color: #27ae60;
            margin-top: 15px;
        }
        .btn {
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
            margin: 10px 5px;
            transition: all 0.3s;
        }
        .btn:hover {
            background-color: #1f6391;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background-color: #95a5a6;
        }
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class='confirmation-card'>
        <div class='success-icon'>✓</div>
        <h1>Booking Confirmed!</h1>
        <p style='color: #7f8c8d; margin-bottom: 20px;'>Your event has been successfully booked.</p>

        <div class='event-details'>
            <div class='detail-row'>
                <span class='detail-label'>Event:</span>
                <span class='detail-value'>" . htmlspecialchars($event_name) . "</span>
            </div>
            <div class='detail-row'>
                <span class='detail-label'>Date:</span>
                <span class='detail-value'>" . htmlspecialchars($event_date) . "</span>
            </div>
            <div class='detail-row'>
                <span class='detail-label'>Location:</span>
                <span class='detail-value'>" . htmlspecialchars($event_location) . "</span>
            </div>
            <div class='price-total'>
                Total: $" . number_format($event_price, 2) . "
            </div>
        </div>

        <a href='my_bookings.php' class='btn'>View My Bookings</a>
        <a href='dashboard.php' class='btn btn-secondary'>Back to Events</a>
    </div>
</body>
</html>";

        $event_check->close();
        $insert->close();
        $update->close();
        $duplicate_check->close();
    } else {
        echo "<script>alert('Event is full or does not exist.'); window.location='dashboard.php';</script>";
    }

    $conn->close();
} else {
    echo "<script>alert('Invalid event ID'); window.location='dashboard.php';</script>";
}
?>
