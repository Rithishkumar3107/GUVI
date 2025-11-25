<?php
header('Content-Type: application/json');
require 'db_mysql_connect.php';  
require 'redisSession.php';       

// Connect to MySQL and Redis
$conn = db_mysql_connect();
$redis = getRedisConnection();

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['success' => false, 'error' => 'Email and password required']);
    exit;
}

// Prepare statement to prevent SQL injection
$stmt = $conn->prepare('SELECT id, password FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid email or password']);
    exit;
}

$user = $result->fetch_assoc();

// Verify password hash
if (!password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid email or password']);
    exit;
}

// Generate session key
$sessionKey = bin2hex(random_bytes(16));

// Store session key in Redis with user ID, expiry, e.g. 1 hour = 3600 seconds
$redis->setex("sess:$sessionKey", 3600, $user['id']);

// Return session key to frontend
echo json_encode(['success' => true, 'sessionKey' => $sessionKey]);
