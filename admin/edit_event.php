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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$event_id = intval($_GET['id']);

if (isset($_POST['update'])) {
    $event_name = trim($_POST['event_name']);
    $description = trim($_POST['description']);
    $date = $_POST['date'];
    $location = trim($_POST['location']);
    $seats = intval($_POST['seats']);
    $price = floatval($_POST['price']);

    $image = $_POST['current_image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($filetype, $allowed)) {
            $newname = uniqid() . '.' . $filetype;
            $upload_path = '../uploads/' . $newname;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                if ($image && file_exists('../uploads/' . $image)) {
                    unlink('../uploads/' . $image);
                }
                $image = $newname;
            }
        }
    }

    $stmt = $conn->prepare("UPDATE events SET event_name=?, description=?, date=?, location=?, seats=?, price=?, image=? WHERE id=?");
    $stmt->bind_param("ssssidsi", $event_name, $description, $date, $location, $seats, $price, $image, $event_id);

    if ($stmt->execute()) {
        echo "<script>alert('Event updated successfully'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating event');</script>";
    }
    $stmt->close();
}

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 700px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }
        h2 {
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
            font-size: 28px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #34495e;
            font-weight: 600;
        }
        input[type="text"],
        input[type="date"],
        input[type="number"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            transition: all 0.3s;
        }
        input:focus, textarea:focus {
            border-color: #2980b9;
            outline: none;
            box-shadow: 0 0 0 3px rgba(41, 128, 185, 0.1);
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        .btn {
            background-color: #2980b9;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #1f6391;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .back-link {
            display: inline-block;
            color: #2980b9;
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .current-image {
            margin-top: 10px;
            max-width: 200px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        <h2>Edit Event</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="current_image" value="<?= htmlspecialchars($event['image']) ?>">

            <div class="form-group">
                <label>Event Name *</label>
                <input type="text" name="event_name" value="<?= htmlspecialchars($event['event_name']) ?>" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description"><?= htmlspecialchars($event['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Date *</label>
                <input type="date" name="date" value="<?= htmlspecialchars($event['date']) ?>" required>
            </div>

            <div class="form-group">
                <label>Location *</label>
                <input type="text" name="location" value="<?= htmlspecialchars($event['location']) ?>" required>
            </div>

            <div class="form-group">
                <label>Available Seats *</label>
                <input type="number" name="seats" min="0" value="<?= htmlspecialchars($event['seats']) ?>" required>
            </div>

            <div class="form-group">
                <label>Price (USD) *</label>
                <input type="number" name="price" step="0.01" min="0" value="<?= htmlspecialchars($event['price']) ?>" required>
            </div>

            <div class="form-group">
                <label>Event Image</label>
                <?php if ($event['image']): ?>
                    <img src="../uploads/<?= htmlspecialchars($event['image']) ?>" alt="Current Image" class="current-image">
                    <p style="color: #666; font-size: 14px; margin-top: 5px;">Upload a new image to replace the current one</p>
                <?php endif; ?>
                <input type="file" name="image" accept="image/*">
            </div>

            <button type="submit" name="update" class="btn">Update Event</button>
        </form>
    </div>
</body>
</html>
