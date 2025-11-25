<?php
// redisSession.php
// Redis connection using phpredis extension

function getRedisConnection() {
    $redis = new Redis();
    try {
        $redis->connect('127.0.0.1', 6379); // default Redis host and port
        // Optionally set Redis auth if configured
        // $redis->auth('your_redis_password');
    } catch (Exception $e) {
        die('Could not connect to Redis: ' . $e->getMessage());
    }
    return $redis;
}
