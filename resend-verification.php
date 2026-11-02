<?php
/**
 * FindNaukri.in - Resend Verification Email
 * Handler for resending OTP verification email
 */

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/otp.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(SITE_URL . '/index.php');
}

// Get user ID and role from URL
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$role = isset($_GET['role']) ? $_GET['role'] : 'candidate';

if (!$userId) {
    setFlash('error', 'Invalid request. Please register again.');
    redirect(SITE_URL . '/index.php');
}

// Verify user exists
$query = "SELECT id, full_name, email, role, email_verified_at FROM users WHERE id = ? LIMIT 1";
$user = fetchOne($query, [$userId]);

if (!$user) {
    setFlash('error', 'User not found. Please register again.');
    redirect(SITE_URL . '/index.php');
}

if ($user['email_verified_at'] !== null) {
    setFlash('error', 'This email has already been verified. You can login to your account.');
    if ($role === ROLE_CANDIDATE) {
        redirect(SITE_URL . '/candidate-login.php');
    } elseif ($role === ROLE_EMPLOYER) {
        redirect(SITE_URL . '/employer-login.php');
    } else {
        redirect(SITE_URL . '/index.php');
    }
}

// Resend OTP
$result = resendOTP($userId, $user['email'], $user['full_name'], 'registration');

if ($result['success']) {
    setFlash('success', 'A new verification code has been sent to your email. Please check your inbox.');
    redirect(SITE_URL . '/verify-email.php?user_id=' . $userId . '&role=' . $role);
} else {
    setFlash('error', $result['message']);
    redirect(SITE_URL . '/verify-email.php?user_id=' . $userId . '&role=' . $role);
}
