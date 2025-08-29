<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

echo "Testing Password Reset Functionality\n";
echo "====================================\n\n";

// Test 1: Check if User model has password reset functionality
echo "1. Checking User model...\n";
$user = new User();
if (method_exists($user, 'sendPasswordResetNotification')) {
    echo "   ✅ User model has password reset functionality\n";
} else {
    echo "   ❌ User model missing password reset functionality\n";
}

// Test 2: Check if password reset tokens table exists
echo "\n2. Checking password reset tokens table...\n";
try {
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('password_reset_tokens');
    if ($tableExists) {
        echo "   ✅ Password reset tokens table exists\n";
    } else {
        echo "   ❌ Password reset tokens table does not exist\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error checking table: " . $e->getMessage() . "\n";
}

// Test 3: Check if a test user exists
echo "\n3. Checking for test user...\n";
$testUser = User::where('email', 'test@example.com')->first();
if ($testUser) {
    echo "   ✅ Test user found: " . $testUser->email . "\n";
} else {
    echo "   ⚠️  No test user found. Creating one...\n";
    $testUser = User::create([
        'name' => 'Test User',
        'display_name' => 'testuser',
        'email' => 'test@example.com',
        'password' => Hash::make('password123')
    ]);
    echo "   ✅ Test user created: " . $testUser->email . "\n";
}

// Test 4: Test password reset token generation
echo "\n4. Testing password reset token generation...\n";
try {
    $status = Password::sendResetLink(['email' => $testUser->email]);
    if ($status === Password::RESET_LINK_SENT) {
        echo "   ✅ Password reset link sent successfully\n";
    } else {
        echo "   ❌ Failed to send password reset link: " . $status . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error sending reset link: " . $e->getMessage() . "\n";
}

// Test 5: Check if token was created in database
echo "\n5. Checking if reset token was created...\n";
try {
    $tokenRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
        ->where('email', $testUser->email)
        ->first();
    
    if ($tokenRecord) {
        echo "   ✅ Reset token created in database\n";
        echo "   📧 Email: " . $tokenRecord->email . "\n";
        echo "   🕒 Created: " . $tokenRecord->created_at . "\n";
    } else {
        echo "   ❌ No reset token found in database\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error checking token: " . $e->getMessage() . "\n";
}

echo "\n====================================\n";
echo "Password Reset Test Complete!\n";
echo "\nTo test the full flow:\n";
echo "1. Start your Laravel server: php artisan serve\n";
echo "2. Visit: http://localhost:8000/forgot-password\n";
echo "3. Enter email: test@example.com\n";
echo "4. Check the logs at: storage/logs/laravel.log\n";

