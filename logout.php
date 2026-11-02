<?php
/**
 * FindNaukri.in - Logout
 */

require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Logout user
logoutUser();

setFlash('success', 'You have been logged out successfully.');
redirect(SITE_URL . '/index.php');
