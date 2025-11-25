<?php
header('Content-Type: application/json');
require 'db_mysql_connect.php';
require 'redisSession.php';
// Uncomment and use for MongoDB if needed
// require 'db_mongo_connect.php';

$conn = db_mysql_connect();
$redis = getRedisConnection();

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$sessionKey = $data['sessionKey'] ?? '';
$action = $data['action'] ?? '';

if (!$sessionKey) {
    echo json_encode(['success' => false, 'error' => 'Session key missing']);
    exit;
}

// Verify session in Redis
$userId = $redis->get("sess:$sessionKey");
if (!$userId) {
    echo json_encode(['success' => false, 'error' => 'Invalid or expired session']);
    exit;
}

if ($action === 'fetch') {
    // Fetch profile from MySQL
    $stmt = $conn->prepare('SELECT username, email, age, dob, contact FROM users WHERE id = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit;
    }

    $user = $result->fetch_assoc();

    // Optionally add MongoDB profile fetch here for extended data

    echo json_encode(['success' => true, 'data' => $user]);
}

elseif ($action === 'update') {
    // Sanitize and fetch input for update
    $username = $data['username'] ?? '';
    $email = $data['email'] ?? '';
    $age = $data['age'] ?? '';
    $dob = $data['dob'] ?? '';
    $contact = $data['contact'] ?? '';

    // Validate inputs as needed...

    $stmt = $conn->prepare('UPDATE users SET username = ?, email = ?, age = ?, dob = ?, contact = ? WHERE id = ?');
    $stmt->bind_param('ssissi', $username, $email, $age, $dob, $contact, $userId);

    if ($stmt->execute()) {
        // Optionally update MongoDB extended profile here

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Update failed']);
    }
}

else {
    echo json_encode(['success' => false, 'error' => 'Unknown action']);
}
