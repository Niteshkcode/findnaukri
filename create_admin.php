<?php
/**
 * Create Admin User Script
 * Run this to create an admin user in the database
 * DELETE THIS FILE AFTER USE!
 */

require_once 'includes/config.php';
require_once 'includes/database.php';

// Admin credentials
$email = 'admin@findnaukri.in';
$password = 'password';
$fullName = 'System Administrator';
$phone = '+91-9876543210';

// Check if admin already exists
$query = "SELECT id FROM users WHERE email = ? LIMIT 1";
$existing = fetchOne($query, [$email]);

if ($existing) {
    echo "<h1>Admin Already Exists</h1>";
    echo "<p>An admin user with email '$email' already exists in the database.</p>";
    echo "<p><strong>Current Admin ID:</strong> " . $existing['id'] . "</p>";

    // Update password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $updateQuery = "UPDATE users SET password_hash = ? WHERE email = ?";
    if (execute($updateQuery, [$passwordHash, $email])) {
        echo "<p style='color: green;'>✓ Password has been reset to: <strong>$password</strong></p>";
    }
} else {
    // Create new admin
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (full_name, email, phone, password_hash, role, status, email_verified_at)
              VALUES (?, ?, ?, ?, 'admin', 'active', NOW())";

    if (execute($query, [$fullName, $email, $phone, $passwordHash])) {
        echo "<h1>Admin User Created Successfully!</h1>";
        echo "<p><strong>Email:</strong> $email</p>";
        echo "<p><strong>Password:</strong> $password</p>";
        echo "<p><strong>Login URL:</strong> <a href='login.php'>login.php</a></p>";
        echo "<p style='color: red;'><strong>IMPORTANT:</strong> Delete this file (create_admin.php) from the server immediately!</p>";
    } else {
        echo "<h1>Error Creating Admin</h1>";
        echo "<p>Failed to create admin user. Please check database connection.</p>";
    }
}

// Show all users
echo "<hr>";
echo "<h2>All Users in Database:</h2>";
$allUsers = fetchAll("SELECT id, full_name, email, role, status FROM users");
if ($allUsers) {
    echo "<table border='1' cellpadding='10'>";
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
?>
