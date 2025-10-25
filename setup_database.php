<?php
include 'db.php';

$conn->query("DROP TABLE IF EXISTS bookings");
$conn->query("DROP TABLE IF EXISTS events");
$conn->query("DROP TABLE IF EXISTS users");

$sql_users = "CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$sql_events = "CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(100) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    location VARCHAR(100) NOT NULL,
    seats INT NOT NULL DEFAULT 0,
    price DECIMAL(10,2) DEFAULT 0.00,
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$sql_bookings = "CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
)";

if ($conn->query($sql_users) === TRUE) {
    echo "Users table created successfully<br>";
} else {
    echo "Error creating users table: " . $conn->error . "<br>";
}

if ($conn->query($sql_events) === TRUE) {
    echo "Events table created successfully<br>";
} else {
    echo "Error creating events table: " . $conn->error . "<br>";
}

if ($conn->query($sql_bookings) === TRUE) {
    echo "Bookings table created successfully<br>";
} else {
    echo "Error creating bookings table: " . $conn->error . "<br>";
}

$admin_password = password_hash('admin123', PASSWORD_DEFAULT);
$admin_insert = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$name = "Admin User";
$email = "admin@example.com";
$role = "admin";
$admin_insert->bind_param("ssss", $name, $email, $admin_password, $role);
if ($admin_insert->execute()) {
    echo "Admin user created successfully (email: admin@example.com, password: admin123)<br>";
}

$sample_events = [
    ['Tech Conference 2025', 'Annual technology conference featuring industry leaders', '2025-11-15', 'Convention Center', 200, 99.99],
    ['Music Festival', 'Live music performances from popular artists', '2025-12-01', 'Central Park', 500, 75.00],
    ['Business Summit', 'Networking event for entrepreneurs and business owners', '2025-11-20', 'Grand Hotel', 150, 150.00],
];

$event_insert = $conn->prepare("INSERT INTO events (event_name, description, date, location, seats, price) VALUES (?, ?, ?, ?, ?, ?)");
foreach ($sample_events as $event) {
    $event_insert->bind_param("ssssid", $event[0], $event[1], $event[2], $event[3], $event[4], $event[5]);
    $event_insert->execute();
}
echo "Sample events created successfully<br>";

echo "<br><strong>Database setup complete!</strong><br>";
echo "<a href='index.php'>Go to Homepage</a> | <a href='login.php'>Go to Login</a>";

$conn->close();
?>
