<?php
// db_mysql_connect.php
// MySQL connection setup using mysqli

function db_mysql_connect() {
    $host = 'localhost';
    $user = 'root';
    $password = 'jenil@1506';
    $dbname = 'gym_db';

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        die('MySQL Connection failed: ' . $conn->connect_error);
    }

    // Set charset to utf8mb4 for full Unicode support
    $conn->set_charset('utf8mb4');

    return $conn;
}
