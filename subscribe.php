<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method');
}

$email = trim($_POST['email'] ?? '');

if ($email === '') {
    die('No email provided');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Invalid email: ' . htmlspecialchars($email));
}

$host = "127.0.0.1";
$db   = "lingu";
$user = "root";
$pass = "Lingaraju@123";
$port = 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    die("DB connection error: " . $conn->connect_error);
}

$conn->query("
    CREATE TABLE IF NOT EXISTS newsletter_subscribers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$stmt = $conn->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "You are already subscribed!";
    $stmt->close();
    $conn->close();
    exit;
}

$stmt->close();
$stmt = $conn->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
$stmt->bind_param("s", $email);

if ($stmt->execute()) {
    echo "Thank you for subscribing!";
} else {
    echo "Insert error: " . $conn->error;
}

$stmt->close();
$conn->close();
