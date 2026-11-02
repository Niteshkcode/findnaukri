# FindNaukri.in - Quick Setup Guide

## 🚀 Quick Start (5 Minutes)

### 1. Database Setup

```bash
# Open phpMyAdmin or use MySQL command line
mysql -u root -p

# Create database
CREATE DATABASE findnaukri_db;

# Import schema
USE findnaukri_db;
SOURCE D:/findNaukri/database/schema.sql;

# Import demo data (optional)
SOURCE D:/findNaukri/database/seed.sql;
```

### 2. Configure Environment

```bash
# Copy environment template
cd D:/findNaukri
copy .env.example .env

# Edit .env file with your database credentials
# Notepad .env
```

Update these values in `.env`:
```env
DB_HOST=localhost
DB_NAME=findnaukri_db
DB_USER=root
DB_PASS=
SITE_URL=http://localhost/findnaukri
```

### 3. Start XAMPP

1. Open XAMPP Control Panel
2. Start Apache
3. Start MySQL

### 4. Access the Application

Open browser: `http://localhost/findnaukri/`

## 🔑 Default Credentials

### Admin
- Email: `admin@findnaukri.in`
- Password: `Admin@123`

### Demo Candidates
- Email: `rahul.sharma@example.com` / Password: `Admin@123`
- Email: `priya.patel@example.com` / Password: `Admin@123`

### Demo Employers (Pending Verification)
- Email: `contact@techsolutions.demo` / Password: `Admin@123`
- Email: `hr@globalservices.demo` / Password: `Admin@123`

**⚠️ IMPORTANT**: Change all default passwords after first login!

## 📁 What's Been Built

### ✅ Completed (Core Foundation)
- ✅ Complete database schema (22 tables)
- ✅ Authentication system (login, register, password reset)
- ✅ Security features (CSRF, validation, prepared statements)
- ✅ Design system (CSS, responsive design)
- ✅ Public pages (index, jobs, job-details, companies, contact, about, services)
- ✅ Candidate dashboard (dashboard, my-applications, saved-jobs)
- ✅ Employer dashboard (dashboard, post-job)
- ✅ Admin dashboard (dashboard)
- ✅ File upload system
- ✅ Shared components (header, footer, functions)

### 🚧 Remaining (Following Same Patterns)
- Candidate: profile.php, edit-profile.php, recommended-jobs.php, job-alerts.php, notifications.php, settings.php
- Employer: company-profile.php, manage-jobs.php, edit-job.php, applicants.php, candidate-details.php, interviews.php, analytics.php, settings.php
- Admin: candidates.php, employers.php, employer-details.php, jobs.php, applications.php, categories.php, articles.php, enquiries.php, reports.php, settings.php, audit-logs.php
- Public: resources.php, article.php, careers.php, error pages
- Email system (PHPMailer integration)

## 🎯 Next Steps to Complete

### 1. Candidate Profile Pages
Follow the pattern in `candidate/dashboard.php`:
```php
requireLogin(ROLE_CANDIDATE);
$userId = getCurrentUserId();
// Fetch user data
// Display form with existing data
// Handle POST to update
```

### 2. Employer Management Pages
Follow the pattern in `employer/dashboard.php`:
```php
requireLogin(ROLE_EMPLOYER);
$userId = getCurrentUserId();
// Fetch employer data
// Display tables/forms
// Handle POST actions
```

### 3. Admin Management Pages
Follow the pattern in `admin/dashboard.php`:
```php
requireLogin(ROLE_ADMIN);
// Fetch statistics/data
// Display tables with actions
// Handle approve/reject operations
```

## 🔧 Common Patterns

### Fetching Data
```php
$query = "SELECT * FROM table WHERE condition = ?";
$result = fetchOne($query, [$param]);
// or
$results = fetchAll($query, [$param]);
```

### Displaying Tables
```php
<div class="table-responsive">
    <table class="table dashboard-table">
        <thead>
            <tr>
                <th>Column 1</th>
                <th>Column 2</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['field']); ?></td>
                <td><?php echo formatDate($row['date']); ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                <button class="btn btn-sm btn-danger">Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
```

### Handling Forms
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $data = sanitizeInput($_POST);
    // Validate
    // Insert/Update
    // Redirect
}
```

## 🐛 Troubleshooting

### Database Connection Error
- Check MySQL is running in XAMPP
- Verify credentials in `.env`
- Ensure database exists and schema is imported

### 404 Errors
- Ensure `.htaccess` is present
- Check mod_rewrite is enabled in Apache
- Verify file paths are correct

### Session Issues
- Check session save path permissions
- Verify session timeout setting
- Clear browser cookies

### File Upload Not Working
- Check upload directory permissions (755)
- Verify PHP upload limits in php.ini
- Check available disk space

## 📚 Key Files Reference

| File | Purpose |
|------|---------|
| `includes/config.php` | Application configuration |
| `includes/database.php` | Database connection and helper functions |
| `includes/auth.php` | Authentication and authorization |
| `includes/functions.php` | Helper functions (formatting, etc.) |
| `includes/validation.php` | Input validation functions |
| `includes/upload.php` | File upload handling |
| `assets/css/style.css` | Main stylesheet |
| `assets/css/dashboard.css` | Dashboard styles |
| `database/schema.sql` | Complete database schema |

## 🎨 Customization

### Change Colors
Edit `assets/css/style.css`:
```css
:root {
    --primary-orange: #F58220;
    --navy-blue: #173F6B;
    /* Update these values */
}
```

### Update Contact Info
Edit `includes/config.php`:
```php
define('SITE_EMAIL', 'your-email@example.com');
define('SITE_PHONE', '+91-XXXXXXXXXX');
define('SITE_ADDRESS', 'Your Address');
```

## 📞 Support

For detailed documentation, see `README.md`

---

**Note**: This is a comprehensive foundation. The remaining pages follow the exact same patterns already established. Use the completed pages as templates to build the rest quickly.
