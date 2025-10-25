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

$total_events = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='user'")->fetch_assoc()['count'];

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';

$query = "SELECT * FROM events WHERE 1=1";
$params = [];
$types = "";

if ($search) {
    $query .= " AND (event_name LIKE ? OR location LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if ($filter_date) {
    $query .= " AND date = ?";
    $params[] = $filter_date;
    $types .= "s";
}

$query .= " ORDER BY date ASC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$events = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        .nav-links a {
            color: #2980b9;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 600;
            transition: color 0.3s;
        }
        .nav-links a:hover {
            color: #1f6391;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card h3 {
            color: #7f8c8d;
            font-size: 14px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .stat-card .number {
            color: #2980b9;
            font-size: 36px;
            font-weight: bold;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }
        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .search-bar input {
            flex: 1;
            min-width: 200px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
        }
        .btn {
            background-color: #2980b9;
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
        }
        .btn:hover {
            background-color: #1f6391;
            transform: translateY(-2px);
        }
        .btn-success {
            background-color: #27ae60;
        }
        .btn-success:hover {
            background-color: #229954;
        }
        .btn-danger {
            background-color: #e74c3c;
            padding: 8px 12px;
            font-size: 14px;
        }
        .btn-danger:hover {
            background-color: #c0392b;
        }
        .btn-warning {
            background-color: #f39c12;
            padding: 8px 12px;
            font-size: 14px;
        }
        .btn-warning:hover {
            background-color: #e67e22;
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
        .event-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }
        .no-image {
            width: 60px;
            height: 60px;
            background-color: #ecf0f1;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #95a5a6;
            font-size: 12px;
        }
        .actions {
            display: flex;
            gap: 8px;
        }
        @media (max-width: 768px) {
            table {
                font-size: 14px;
            }
            .event-image, .no-image {
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Dashboard</h1>
        <div class="nav-links">
            <a href="view_bookings.php">View Bookings</a>
            <a href="../index.php">Home</a>
            <a href="../user/logout.php">Logout</a>
        </div>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <h3>Total Events</h3>
            <div class="number"><?= $total_events ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Bookings</h3>
            <div class="number"><?= $total_bookings ?></div>
        </div>
        <div class="stat-card">
            <h3>Registered Users</h3>
            <div class="number"><?= $total_users ?></div>
        </div>
    </div>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap;">
            <h2 style="color: #2c3e50;">Manage Events</h2>
            <a href="add_event.php" class="btn btn-success">+ Add New Event</a>
        </div>

        <form method="GET" class="search-bar">
            <input type="text" name="search" placeholder="Search by name or location..." value="<?= htmlspecialchars($search) ?>">
            <input type="date" name="filter_date" value="<?= htmlspecialchars($filter_date) ?>">
            <button type="submit" class="btn">Search</button>
            <a href="dashboard.php" class="btn" style="background-color: #95a5a6;">Clear</a>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Seats</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $events->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php if ($row['image']): ?>
                            <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="Event" class="event-image">
                        <?php else: ?>
                            <div class="no-image">No Image</div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['event_name']) ?></td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                    <td><?= htmlspecialchars($row['location']) ?></td>
                    <td><?= htmlspecialchars($row['seats']) ?></td>
                    <td>$<?= number_format($row['price'], 2) ?></td>
                    <td>
                        <div class="actions">
                            <a href="edit_event.php?id=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                            <a href="delete_event.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
