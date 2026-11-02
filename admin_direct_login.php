<?php
/**
 * Direct Admin Login - Bypass Script
 * This script directly sets admin session for login
 * DELETE THIS FILE IMMEDIATELY AFTER USE!
 */

// Start session
session_start();

// Database connection
require_once 'includes/config.php';
require_once 'includes/database.php';

// Get admin user from database
$email = 'admin@findnaukri.in';
$query = "SELECT id, full_name, email, role, status FROM users WHERE email = ? AND role = 'admin' LIMIT 1";
$admin = fetchOne($query, [$email]);

if (!$admin) {
    // Create admin if doesn't exist
    $password = 'password';
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $insertQuery = "INSERT INTO users (full_name, email, phone, password_hash, role, status, email_verified_at)
                    VALUES (?, ?, ?, ?, 'admin', 'active', NOW())";

    if (execute($insertQuery, ['System Administrator', $email, '+91-9876543210', $passwordHash])) {
        $admin = fetchOne($query, [$email]);
    } else {
        die("Failed to create admin user");
    }
}

// Set session variables
$_SESSION['user_id'] = $admin['id'];
$_SESSION['user_name'] = $admin['full_name'];
$_SESSION['user_email'] = $admin['email'];
$_SESSION['user_role'] = $admin['role'];
$_SESSION['logged_in'] = true;
$_SESSION['login_time'] = time();

// Redirect to admin dashboard
header('Location: admin/dashboard.php');
exit;
?>
