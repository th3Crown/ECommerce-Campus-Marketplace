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
$email = trim($_POST['email'] ?? '');
$pass = $_POST['password'] ?? '';
$acceptTerms = isset($_POST['acceptTerms']) ? true : false;

if (empty($user) || empty($email) || empty($pass)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}
if (!$acceptTerms) {
    echo json_encode(['success' => false, 'message' => 'You must accept the terms and conditions.']);
    exit;
}
if (strlen($pass) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->execute([$user, $email]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Username or email already exists.']);
    exit;
}

$hashedPass = password_hash($pass, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
if ($stmt->execute([$user, $email, $hashedPass])) {
    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['username'] = $user;
    echo json_encode(['success' => true, 'message' => 'Account created successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create account.']);
}
?>