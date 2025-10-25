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

    $event_check = $conn->prepare("SELECT seats FROM events WHERE id = ? AND seats > 0");
    $event_check->bind_param("i", $event_id);
    $event_check->execute();
    $event_check->store_result();

    if ($event_check->num_rows > 0) {
        $insert = $conn->prepare("INSERT INTO bookings (user_id, event_id) VALUES (?, ?)");
        $insert->bind_param("ii", $user_id, $event_id);
        $insert->execute();

        $update = $conn->prepare("UPDATE events SET seats = seats - 1 WHERE id = ?");
        $update->bind_param("i", $event_id);
        $update->execute();

        echo "<script>alert('Event booked successfully!'); window.location='my_bookings.php';</script>";
    } else {
        echo "<script>alert('Event is full or does not exist.'); window.location='dashboard.php';</script>";
    }

    $event_check->close();
    $insert->close();
    $update->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid event ID'); window.location='dashboard.php';</script>";
}
?>
