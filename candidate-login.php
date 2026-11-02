<?php
/**
 * FindNaukri.in - Candidate Login (Redirect to new unified login)
 * This file now redirects to the new unified login page
 */

require_once 'includes/config.php';

// Redirect to new unified login page with candidate tab active
header('Location: ' . SITE_URL . '/login.php?tab=candidate');
exit;
