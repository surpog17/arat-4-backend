<?php

// Simple script to check if WebSocket server is running
$host = '127.0.0.1';
$port = 6001;

echo "Checking WebSocket server at {$host}:{$port}...\n";

$connection = @fsockopen($host, $port, $errno, $errstr, 5);

if ($connection) {
    echo "✅ WebSocket server is running and accessible\n";
    fclose($connection);
} else {
    echo "❌ WebSocket server is not accessible: {$errstr} ({$errno})\n";
    echo "Make sure to run: php artisan websockets:serve\n";
}

// Also check if broadcasting is enabled
echo "\nChecking broadcasting configuration...\n";
$broadcastDriver = env('BROADCAST_DRIVER', 'null');
echo "Broadcast driver: {$broadcastDriver}\n";

if ($broadcastDriver === 'pusher') {
    echo "✅ Broadcasting is configured for Pusher\n";
} else {
    echo "❌ Broadcasting is not configured for Pusher\n";
    echo "Set BROADCAST_DRIVER=pusher in your .env file\n";
}
