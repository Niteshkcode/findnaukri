<?php
/**
 * FindNaukri.in - Employer Login (Redirect to new unified login)
 * This file now redirects to the new unified login page
 */

require_once 'includes/config.php';

// Redirect to new unified login page with employer tab active
header('Location: ' . SITE_URL . '/login.php?tab=employer');
exit;
