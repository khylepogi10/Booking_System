<?php
session_start();
include '../db.php';
if ($_SESSION['role'] != 'admin') { header("Location: ../login.php"); exit; }

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$event_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT image FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if ($event && $event['image'] && file_exists('../uploads/' . $event['image'])) {
    unlink('../uploads/' . $event['image']);
}

$delete = $conn->prepare("DELETE FROM events WHERE id = ?");
$delete->bind_param("i", $event_id);

if ($delete->execute()) {
    echo "<script>alert('Event deleted successfully'); window.location='dashboard.php';</script>";
} else {
    echo "<script>alert('Error deleting event'); window.location='dashboard.php';</script>";
}

$stmt->close();
$delete->close();
$conn->close();
?>
