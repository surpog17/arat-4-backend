<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4-Digit Number Guessing Game API</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .endpoint {
            background: #f5f5f5;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .method {
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎮 4-Digit Number Guessing Game API</h1>
        <p>Welcome to the multiplayer number guessing game API!</p>
    </div>

    <h2>Available Endpoints:</h2>
    
    <div class="endpoint">
        <span class="method">POST</span> /api/register - Register new user account
    </div>
    
    <div class="endpoint">
        <span class="method">POST</span> /api/login - Login with email and password
    </div>
    
    <div class="endpoint">
        <span class="method">POST</span> /api/rooms - Create a new game room
    </div>
    
    <div class="endpoint">
        <span class="method">POST</span> /api/rooms/{id}/join - Join an existing room
    </div>
    
    <div class="endpoint">
        <span class="method">POST</span> /api/rooms/{id}/secret - Set your secret number
    </div>
    
    <div class="endpoint">
        <span class="method">POST</span> /api/rooms/{id}/guess - Submit a guess
    </div>
    
    <div class="endpoint">
        <span class="method">GET</span> /api/history - View game history
    </div>

    <p><strong>Note:</strong> This is an API-only application. Use the React frontend to play the game!</p>
</body>
</html>
<?php /**PATH C:\Users\IENetwork\Desktop\number-guessing-game\backend\resources\views/welcome.blade.php ENDPATH**/ ?>