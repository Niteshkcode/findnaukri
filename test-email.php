<?php
/**
 * Test Email Configuration
 * Use this file to test if SMTP is working correctly
 */

require_once 'includes/config.php';
require_once 'includes/mailer.php';

// Test email configuration
$testEmail = 'niteshydv@gmail.com'; // Change to your personal email for testing
$subject = 'FindNaukri.in - cPanel SMTP Test';
$body = '
<h1>Email Test Successful!</h1>
<p>If you receive this email, your cPanel SMTP configuration is working correctly.</p>
<p><strong>SMTP Configuration:</strong></p>
<ul>
    <li>Host: mail.findnaukri.in</li>
    <li>Port: 465</li>
    <li>Encryption: SSL</li>
    <li>From: support@findnaukri.in</li>
</ul>
<p>This confirms that your email OTP system will work when deployed to cPanel.</p>
<p><em>Test sent from: ' . SITE_URL . '</em></p>
';

echo "<h2>Testing Email Configuration...</h2>";
echo "<p>Sending test email to: <strong>" . htmlspecialchars($testEmail) . "</strong></p>";
echo "<p>Using SMTP: mail.findnaukri.in:465 (SSL)</p>";

$result = sendEmail($testEmail, $subject, $body);

if ($result['success']) {
    echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>✅ Email Sent Successfully!</h3>";
    echo "<p>Please check your inbox (and spam folder) for the test email.</p>";
    echo "</div>";
} else {
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>❌ Email Failed</h3>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($result['message']) . "</p>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>SMTP credentials in .env are correct</li>";
    echo "<li>Email account exists in cPanel</li>";
    echo "<li>Password is correct</li>";
    echo "<li>PHP extensions (openssl, mbstring) are enabled</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<hr>";
echo "<p><strong>Configuration Details:</strong></p>";
echo "<pre>";
echo "MAIL_HOST = " . getenv('MAIL_HOST') . "\n";
echo "MAIL_PORT = " . getenv('MAIL_PORT') . "\n";
echo "MAIL_USERNAME = " . getenv('MAIL_USERNAME') . "\n";
echo "MAIL_ENCRYPTION = " . getenv('MAIL_ENCRYPTION') . "\n";
echo "MAIL_FROM_ADDRESS = " . getenv('MAIL_FROM_ADDRESS') . "\n";
echo "</pre>";

echo "<hr>";
echo "<p><a href='index.php'>← Back to Home</a></p>";
echo "<p><em>Note: Delete this file after testing for security reasons.</em></p>";
