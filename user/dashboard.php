<?php
session_start();
include(__DIR__ . '/../db.php');

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

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';

$query = "SELECT id, event_name, date, location, seats, description, price, image FROM events WHERE 1=1";
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

$events = $conn->prepare($query);
if (!empty($params)) {
    $events->bind_param($types, ...$params);
}
$events->execute();
$result = $events->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
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
        .search-bar {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: flex;
            gap: 10px;
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
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }
        .event-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .event-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }
        .event-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .no-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            font-weight: 600;
        }
        .event-content {
            padding: 20px;
        }
        .event-title {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 10px;
            font-weight: 700;
        }
        .event-info {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .event-description {
            color: #7f8c8d;
            font-size: 14px;
            margin: 15px 0;
            line-height: 1.5;
        }
        .event-price {
            font-size: 24px;
            color: #27ae60;
            font-weight: bold;
            margin: 15px 0;
        }
        .event-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }
        .seats-left {
            font-size: 14px;
            color: #e74c3c;
            font-weight: 600;
        }
        .sold-out {
            background-color: #e74c3c;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .events-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to Event Booking</h1>
        <div class="nav-links">
            <a href="my_bookings.php">My Bookings</a>
            <a href="../index.php">Home</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Search by name or location..." value="<?= htmlspecialchars($search) ?>">
        <input type="date" name="filter_date" value="<?= htmlspecialchars($filter_date) ?>">
        <button type="submit" class="btn">Search</button>
        <a href="dashboard.php" class="btn" style="background-color: #95a5a6;">Clear</a>
    </form>

    <div class="events-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="event-card">
            <?php if ($row['image']): ?>
                <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['event_name']) ?>" class="event-image">
            <?php else: ?>
                <div class="no-image"><?= htmlspecialchars(substr($row['event_name'], 0, 1)) ?></div>
            <?php endif; ?>

            <div class="event-content">
                <div class="event-title"><?= htmlspecialchars($row['event_name']) ?></div>

                <div class="event-info">
                    <strong>Date:</strong> <?= htmlspecialchars($row['date']) ?>
                </div>

                <div class="event-info">
                    <strong>Location:</strong> <?= htmlspecialchars($row['location']) ?>
                </div>

                <?php if ($row['description']): ?>
                    <div class="event-description"><?= htmlspecialchars($row['description']) ?></div>
                <?php endif; ?>

                <div class="event-price">$<?= number_format($row['price'], 2) ?></div>

                <div class="event-footer">
                    <div class="seats-left">
                        <?php if ($row['seats'] > 0): ?>
                            <?= $row['seats'] ?> seats left
                        <?php else: ?>
                            Sold Out
                        <?php endif; ?>
                    </div>

                    <?php if ($row['seats'] > 0): ?>
                        <a href="book_event.php?id=<?= $row['id'] ?>" class="btn">Book Now</a>
                    <?php else: ?>
                        <span class="sold-out">Sold Out</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
