# Email OTP Verification - Implementation Guide

## Overview

This guide will help you set up and test the email OTP verification system for FindNaukri.in. The system has been fully implemented and is ready for configuration and testing.

---

## ✅ What Has Been Implemented

### 1. **PHPMailer Integration**
- PHPMailer 6.8.1 installed in `vendor/phpmailer/`
- Email sending functionality in `includes/mailer.php`
- SMTP configuration from `.env` file

### 2. **OTP System**
- OTP generation and validation in `includes/otp.php`
- Database table `email_otp_tokens` for storing OTP tokens
- 6-digit numeric OTP codes
- 15-minute expiry time
- Maximum 3 attempts per OTP
- Rate limiting (max 3 resends per hour)

### 3. **Email Templates**
- Professional HTML email templates in `email-templates/`
- Theme: White, warm cream (#FFF6ED), charcoal (#343434), orange (#F58220)
- Templates created:
  - `otp-verification.html` - OTP verification email
  - `welcome-candidate.html` - Welcome email for candidates
  - `welcome-employer.html` - Welcome email for employers
  - `login-notification.html` - Login security alert
  - `password-reset.html` - Password reset email

### 4. **Registration Flow Updated**
- Candidate registration now sends OTP email
- Employer registration now sends OTP email
- Users redirected to verification page
- Removed development auto-verification

### 5. **Verification Pages**
- `verify-email.php` - OTP entry page
- `resend-verification.php` - Resend OTP handler
- Login pages updated with resend verification links

### 6. **Login Notifications**
- Email sent after successful login
- Includes IP address, date, time, location
- Security alert for unauthorized access detection

---

## 🔧 Setup Instructions

### Step 1: Configure SMTP Settings

Update your `.env` file with your SMTP credentials:

```env
# Email Configuration (SMTP)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_NAME=FindNaukri.in
MAIL_FROM_ADDRESS=noreply@findnaukri.in
```

#### For Gmail (Recommended for Testing):

1. Go to [Google Account Settings](https://myaccount.google.com/)
2. Enable **2-Factor Authentication** (if not already enabled)
3. Go to **Security** > **App Passwords**
4. Click **Select app** → Choose **Mail**
5. Click **Select device** → Choose your device
6. Click **Generate**
7. Copy the 16-character password (format: `abcd efgh ijkl mnop`)
8. Paste it in `.env` as `MAIL_PASSWORD` (without spaces)

**Example:**
```env
MAIL_USERNAME=findnaukri.test@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop
```

#### For Production SMTP Providers:

**SendGrid:**
```env
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.your-sendgrid-api-key
MAIL_ENCRYPTION=tls
```

**Mailgun:**
```env
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@yourdomain.com
MAIL_PASSWORD=your-mailgun-password
MAIL_ENCRYPTION=tls
```

**Hosting Provider SMTP (cPanel):**
```env
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=465
MAIL_USERNAME=info@yourdomain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=ssl
```

---

### Step 2: Run Database Migration

Import the OTP table into your database:

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select `findnaukri_db` database
3. Click **Import** tab
4. Choose file: `database/email_otp_migration.sql`
5. Click **Go**

Or run via command line:
```bash
mysql -u root -p findnaukri_db < database/email_otp_migration.sql
```

**This will NOT affect any existing data.** It only adds the `email_otp_tokens` table.

---

### Step 3: Verify PHP Extensions

Ensure these PHP extensions are enabled in XAMPP:

1. Open `C:\xampp\php\php.ini`
2. Search for and uncomment (remove `;`):
   ```ini
   extension=openssl
   extension=mbstring
   extension=fileinfo
   ```
3. Save the file
4. Restart Apache from XAMPP Control Panel

---

### Step 4: Test SMTP Connection

Create a test file `test-email.php` in your project root:

```php
<?php
require_once 'includes/config.php';
require_once 'includes/mailer.php';

$result = sendEmail('your-test-email@gmail.com', 'Test Email', '<h1>Test Email</h1><p>If you receive this, email is working!</p>');

if ($result['success']) {
    echo "Email sent successfully!";
} else {
    echo "Email failed: " . $result['message'];
}
```

Access: `http://localhost/findnaukri/test-email.php`

If successful, delete the test file after verification.

---

## 🧪 Testing the Complete Flow

### Test 1: Candidate Registration with OTP

1. **Register New Candidate**
   - Go to: `http://localhost/findnaukri/candidate-register.php`
   - Fill in registration form with new email
   - Click **Register**

2. **Check Email**
   - You should receive an email with 6-digit OTP
   - Subject: "Verify Your Email Address - FindNaukri.in"

3. **Verify Email**
   - Enter the 6-digit code on verification page
   - Click **Verify Email**
   - You should be redirected to candidate dashboard
   - You should receive a welcome email

4. **Login**
   - Logout and login again
   - You should receive a login notification email

### Test 2: Employer Registration with OTP

1. **Register New Employer**
   - Go to: `http://localhost/findnaukri/employer-register.php`
   - Fill in registration form with new email
   - Click **Register**

2. **Verify Email**
   - Same process as candidate
   - Redirected to employer dashboard after verification

### Test 3: Resend OTP

1. During verification, click **Resend Verification Code**
2. Check email for new OTP
3. Verify with new code

### Test 4: Password Reset

1. Go to login page
2. Click **Forgot Password?**
3. Enter email
4. Check email for reset link
5. Reset password

### Test 5: Existing Accounts

1. Try logging in with existing accounts (from seed.sql)
2. Should work without OTP (already verified)
3. Should receive login notification email

---

## 🐛 Troubleshooting

### Issue: Email Not Sending

**Symptoms:**
- Registration successful but no email received
- Error message about SMTP

**Solutions:**
1. Check `.env` SMTP credentials are correct
2. For Gmail, ensure you're using App Password (not regular password)
3. Check if 2FA is enabled on Gmail account
4. Try different SMTP port (587 for TLS, 465 for SSL)
5. Check XAMPP error logs: `C:\xampp\apache\logs\error.log`

### Issue: OTP Expired

**Symptoms:**
- "Invalid or expired verification code" error

**Solutions:**
1. OTP expires in 15 minutes
2. Click "Resend Verification Code" to get new OTP
3. Check email immediately after requesting

### Issue: Maximum Attempts Exceeded

**Symptoms:**
- "Maximum attempts exceeded" error

**Solutions:**
1. Wait 1 hour before requesting new OTP
2. Or request password reset instead

### Issue: Database Connection Error

**Symptoms:**
- Database connection fails after migration

**Solutions:**
1. Verify migration was successful
2. Check table exists: `SHOW TABLES LIKE 'email_otp_tokens';`
3. Re-run migration if needed

### Issue: PHP Extensions Missing

**Symptoms:**
- Fatal error about missing PHPMailer classes

**Solutions:**
1. Verify PHPMailer files exist in `vendor/phpmailer/src/`
2. Enable required PHP extensions (openssl, mbstring)
3. Restart Apache

---

## 📊 Database Schema

### New Table: email_otp_tokens

```sql
CREATE TABLE `email_otp_tokens` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `otp_code` VARCHAR(10) NOT NULL,
  `otp_hash` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `purpose` ENUM('registration', 'email_change', 'password_reset') NOT NULL DEFAULT 'registration',
  `expires_at` TIMESTAMP NOT NULL,
  `used_at` TIMESTAMP NULL DEFAULT NULL,
  `attempts` INT UNSIGNED DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_otp_user` (`user_id`),
  KEY `idx_otp_hash` (`otp_hash`),
  KEY `idx_otp_expires` (`expires_at`),
  CONSTRAINT `fk_otp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🔒 Security Features

1. **OTP Hashing** - OTPs are hashed before storage (SHA-256)
2. **Expiry Time** - OTPs expire in 15 minutes
3. **Attempt Limiting** - Maximum 3 attempts per OTP
4. **Rate Limiting** - Maximum 3 resends per hour
5. **Secure Session** - Session regeneration after login
6. **CSRF Protection** - All forms protected with CSRF tokens
7. **Audit Logging** - All OTP actions logged

---

## 📁 Files Created/Modified

### New Files:
- `vendor/phpmailer/` - PHPMailer library
- `email-templates/otp-verification.html`
- `email-templates/welcome-candidate.html`
- `email-templates/welcome-employer.html`
- `email-templates/login-notification.html`
- `email-templates/password-reset.html`
- `includes/mailer.php` - Email sending functions
- `includes/otp.php` - OTP functions
- `verify-email.php` - Verification page
- `resend-verification.php` - Resend handler
- `database/email_otp_migration.sql` - Database migration

### Modified Files:
- `includes/functions.php` - Updated sendEmail() function
- `includes/auth.php` - Added login notification email
- `candidate-register.php` - OTP flow integration
- `employer-register.php` - OTP flow integration
- `candidate-login.php` - Added resend verification link
- `employer-login.php` - Added resend verification link
- `.env` - Updated SMTP configuration comments

---

## 🚀 Production Deployment Checklist

Before deploying to production:

- [ ] Update `.env` with production SMTP credentials
- [ ] Change `ENVIRONMENT=production` in `.env`
- [ ] Set `DEBUG=false` in `.env`
- [ ] Update `ENCRYPTION_KEY` with strong random string
- [ ] Update `MAIL_FROM_ADDRESS` with production domain
- [ ] Test email sending with production SMTP
- [ ] Remove any test files
- [ ] Change default admin password
- [ ] Remove or secure demo data from seed.sql
- [ ] Enable HTTPS (SSL certificate)
- [ ] Set proper file permissions (755 for uploads)
- [ ] Test complete registration flow
- [ ] Test password reset flow
- [ ] Test login notifications

---

## 📞 Support

If you encounter issues:

1. Check XAMPP error logs: `C:\xampp\apache\logs\error.log`
2. Check PHP error logs: `C:\xampp\php\logs\php_error_log`
3. Verify SMTP credentials are correct
4. Test SMTP connection with external tool (e.g., telnet)
5. Check email spam folder

---

## ✨ Features Summary

### ✅ Implemented:
- Email OTP verification for registration
- Welcome emails after verification
- Login notification emails
- Password reset emails
- Resend OTP functionality
- Rate limiting
- Professional email templates
- PHPMailer integration
- Secure OTP storage
- Audit logging

### 🔜 Future Enhancements (Optional):
- SMS OTP as alternative
- Email queue system for high volume
- IP geolocation for login notifications
- 2FA (Two-Factor Authentication) option
- Email preference settings
- Bounce handling and retry logic

---

## 🎉 Congratulations!

Your FindNaukri.in website now has a complete email OTP verification system. All existing accounts remain unaffected, and new registrations will use the secure OTP verification flow.

**Next Steps:**
1. Configure SMTP credentials in `.env`
2. Run database migration
3. Test with a new registration
4. Deploy to production when ready

---

*Generated: 2026-10-25*
*Implementation: Email OTP Verification System*
