<?php
/**
 * Admin Login Diagnostic Script
 * Helps identify why admin login is failing
 * DELETE THIS FILE AFTER USE!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Admin Login Diagnostic</h1>";
echo "<hr>";

// Test 1: Check .env file
echo "<h2>1. Configuration Check</h2>";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "<p style='color: green;'>✓ .env file exists</p>";
    require_once __DIR__ . '/includes/config.php';
    echo "<p>DB_HOST: " . DB_HOST . "</p>";
    echo "<p>DB_NAME: " . DB_NAME . "</p>";
    echo "<p>DB_USER: " . DB_USER . "</p>";
} else {
    echo "<p style='color: red;'>✗ .env file not found!</p>";
    die("Please upload .env file first.");
}

// Test 2: Database Connection
echo "<h2>2. Database Connection</h2>";
try {
    require_once __DIR__ . '/includes/database.php';
    $db = getDB();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    die("Fix database connection first.");
}

// Test 3: Check if users table exists
echo "<h2>3. Database Tables</h2>";
try {
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Tables found: " . implode(', ', $tables) . "</p>";

    if (!in_array('users', $tables)) {
        echo "<p style='color: red;'>✗ 'users' table not found! Import schema.sql first.</p>";
        die("Import database/schema.sql via phpMyAdmin.");
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error checking tables: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test 4: Check for admin user
echo "<h2>4. Admin User Check</h2>";
$adminEmail = 'admin@findnaukri.in';
$query = "SELECT id, full_name, email, password_hash, role, status FROM users WHERE email = ? LIMIT 1";
$admin = fetchOne($query, [$adminEmail]);

if ($admin) {
    echo "<p style='color: green;'>✓ Admin user found</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr>";
    echo "<tr>";
    echo "<td>" . $admin['id'] . "</td>";
    echo "<td>" . htmlspecialchars($admin['full_name']) . "</td>";
    echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
    echo "<td>" . htmlspecialchars($admin['role']) . "</td>";
    echo "<td>" . htmlspecialchars($admin['status']) . "</td>";
    echo "</tr>";
    echo "</table>";

    if ($admin['status'] !== 'active') {
        echo "<p style='color: red;'>⚠ Admin status is: " . htmlspecialchars($admin['status']) . " (should be 'active')</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Admin user NOT found in database</p>";
    echo "<p>Creating admin user now...</p>";

    $password = 'password';
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $insertQuery = "INSERT INTO users (full_name, email, phone, password_hash, role, status, email_verified_at)
                    VALUES (?, ?, ?, ?, 'admin', 'active', NOW())";

    if (execute($insertQuery, ['System Administrator', $adminEmail, '+91-9876543210', $passwordHash])) {
        echo "<p style='color: green;'>✓ Admin user created successfully!</p>";
        echo "<p><strong>Email:</strong> $adminEmail</p>";
        echo "<p><strong>Password:</strong> $password</p>";
        $admin = fetchOne($query, [$adminEmail]);
    } else {
        echo "<p style='color: red;'>✗ Failed to create admin user</p>";
    }
}

// Test 5: Test password verification
if ($admin) {
    echo "<h2>5. Password Verification Test</h2>";
    $testPassword = 'password';
    $passwordHash = $admin['password_hash'];

    echo "<p>Testing password: '$testPassword'</p>";
    echo "<p>Password hash: " . substr($passwordHash, 0, 50) . "...</p>";

    if (password_verify($testPassword, $passwordHash)) {
        echo "<p style='color: green;'>✓ Password verification SUCCESSFUL</p>";
    } else {
        echo "<p style='color: red;'>✗ Password verification FAILED</p>";
        echo "<p>Resetting password to 'password'...</p>";

        $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
        $updateQuery = "UPDATE users SET password_hash = ? WHERE email = ?";
        if (execute($updateQuery, [$newHash, $adminEmail])) {
            echo "<p style='color: green;'>✓ Password reset successful</p>";

            // Test again
            $admin = fetchOne($query, [$adminEmail]);
            if (password_verify($testPassword, $admin['password_hash'])) {
                echo "<p style='color: green;'>✓ Password verification now WORKS</p>";
            }
        }
    }
}

// Test 6: Show all users
echo "<h2>6. All Users in Database</h2>";
$allUsers = fetchAll("SELECT id, full_name, email, role, status FROM users");
if ($allUsers) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr>";
    foreach ($allUsers as $user) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
        echo "<td>" . htmlspecialchars($user['role']) . "</td>";
        echo "<td>" . htmlspecialchars($user['status']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No users found in database.</p>";
}

// Test 7: Login instructions
echo "<hr>";
echo "<h2>7. Login Instructions</h2>";
echo "<p><strong>Login URL:</strong> <a href='login.php'>login.php</a></p>";
echo "<p><strong>Email:</strong> admin@findnaukri.in</p>";
echo "<p><strong>Password:</strong> password</p>";
echo "<p style='color: red;'><strong>IMPORTANT:</strong> Delete this file (admin_diagnostic.php) from the server immediately after fixing!</p>";
?>
