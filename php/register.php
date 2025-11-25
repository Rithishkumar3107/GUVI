<?php
header('Content-Type: application/json');
require 'db_mysql_connect.php';


$conn = db_mysql_connect();

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$age = $data['age'] ?? '';
$dob = $data['dob'] ?? '';
$contact = trim($data['contact'] ?? '');

// Basic validation
if (!$username || !$email || !$password || !$age || !$dob || !$contact) {
    echo json_encode(['success' => false, 'error' => 'All fields are required']);
    exit;
}

// Check if email already exists to prevent duplicates
$stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Email already registered']);
    exit;
}
$stmt->close();

// Hash the password securely
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Insert into MySQL users table
$stmt = $conn->prepare('INSERT INTO users (username, email, password, age, dob, contact) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->bind_param('sssiss', $username, $email, $hashedPassword, $age, $dob, $contact);

if ($stmt->execute()) {
    // Optionally store extended profile in MongoDB here

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Registration failed']);
}
