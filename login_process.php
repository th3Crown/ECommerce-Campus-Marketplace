<?php
header('Content-Type: application/json');
session_start();

$host = 'localhost';
$dbname = 'marketplace_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$user = trim($_POST['username'] ?? '');
$pass = $_POST['password'] ?? '';

if (empty($user) || empty($pass)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required.']);
    exit;
}

$stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
$stmt->execute([$user, $user]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if ($userData && password_verify($pass, $userData['password'])) {
    $_SESSION['user_id'] = $userData['id'];
    $_SESSION['username'] = $userData['username'];
    echo json_encode(['success' => true, 'message' => 'Login successful.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
}
?>