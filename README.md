# FindNaukri.in - Professional Job Portal & Recruitment Platform

A complete, modern, responsive, database-driven job portal built with PHP, MySQL, Bootstrap 5, and vanilla JavaScript. FindNaukri.in connects job seekers with employers through an efficient, transparent hiring process.

## 🚀 Features

### For Job Seekers
- **User Registration & Authentication**: Secure registration with email verification
- **Profile Management**: Complete profile with education, experience, skills, and projects
- **Resume Upload**: Secure PDF/DOC resume upload with management
- **Job Search**: Advanced search with filters for category, location, experience, salary, etc.
- **Job Applications**: Apply to jobs and track application status
- **Saved Jobs**: Bookmark jobs for later review
- **Job Alerts**: Email notifications for matching jobs
- **Application Tracking**: Real-time status updates with timeline

### For Employers
- **Company Registration**: Verified employer accounts
- **Job Posting**: Create and manage job listings
- **Applicant Management**: View, filter, and manage applications
- **Candidate Profiles**: Access detailed candidate information
- **Status Updates**: Update application status (Applied → Shortlisted → Interview → Selected)
- **Interview Scheduling**: Schedule and manage interviews
- **Analytics**: Dashboard with recruitment metrics

### For Administrators
- **User Management**: Manage candidates, employers, and admin accounts
- **Employer Verification**: Approve or reject employer registrations
- **Job Approval**: Review and approve/reject job postings
- **Content Management**: Manage categories, articles, and platform content
- **Reports & Analytics**: Platform-wide statistics and trends
- **Audit Logs**: Track all administrative actions
- **Settings**: Configure platform settings and preferences

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Backend**: Core PHP 8+
- **Database**: MySQL 8+
- **Database Connectivity**: PDO with prepared statements
- **Icons**: Font Awesome 6
- **Charts**: Chart.js for dashboard analytics
- **Authentication**: PHP sessions, password_hash(), role-based access control
- **Security**: CSRF protection, input validation, prepared statements, secure file uploads

## 📋 Prerequisites

- PHP 8.0 or higher
- MySQL 8.0 or higher
- Apache web server (with mod_rewrite enabled)
- Composer (optional, for dependency management)

## 📦 Installation

### 1. Local Development (XAMPP)

#### Step 1: Download XAMPP
Download and install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)

#### Step 2: Clone/Download Project
Place the `findnaukri` folder in:
```
C:\xampp\htdocs\findnaukri\
```
Or on Linux/Mac:
```
/opt/lampp/htdocs/findnaukri/
```

#### Step 3: Start Apache and MySQL
- Open XAMPP Control Panel
- Start Apache
- Start MySQL

#### Step 4: Create Database
- Open phpMyAdmin at `http://localhost/phpmyadmin`
- Create a new database named `findnaukri_db`
- Import `database/schema.sql`
- (Optional) Import `database/seed.sql` for demo data

#### Step 5: Configure Environment
Copy `.env.example` to `.env` and update:
```env
DB_HOST=localhost
DB_NAME=findnaukri_db
DB_USER=root
DB_PASS=
SITE_URL=http://localhost/findnaukri
```

#### Step 6: Set Permissions
Ensure upload directories are writable:
```bash
chmod 755 uploads/
chmod 755 uploads/resumes/
chmod 755 uploads/profiles/
chmod 755 uploads/company-logos/
```

#### Step 7: Access the Application
Open your browser and navigate to:
```
http://localhost/findnaukri/
```

### 2. Production Deployment (Shared Hosting)

#### Step 1: Upload Files
Upload all files to your hosting public directory (usually `public_html` or `www`).

#### Step 2: Create Database
- Use your hosting control panel (cPanel, Plesk, etc.)
- Create a new MySQL database
- Import `database/schema.sql`
- (Optional) Import `database/seed.sql`

#### Step 3: Configure Environment
Create `.env` file with your production credentials:
```env
DB_HOST=your-hosting-db-host
DB_NAME=your-database-name
DB_USER=your-database-user
DB_PASS=your-database-password
SITE_URL=https://yourdomain.com
```

#### Step 4: Configure Email
Update email settings in `.env` for production:
```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

#### Step 5: Set File Permissions
Ensure upload directories are writable (755).

#### Step 6: Test Configuration
Visit your domain and test all functionality.

## 👤 Default Accounts

### Administrator
- **Email**: admin@findnaukri.in
- **Password**: Admin@123
- **⚠️ IMPORTANT**: Change this password immediately after first login!

### Demo Candidates (from seed.sql)
- Email: rahul.sharma@example.com / Password: Admin@123
- Email: priya.patel@example.com / Password: Admin@123
- Email: amit.kumar@example.com / Password: Admin@123
- Email: sneha.reddy@example.com / Password: Admin@123

### Demo Employers (from seed.sql - pending verification)
- Email: contact@techsolutions.demo / Password: Admin@123
- Email: hr@globalservices.demo / Password: Admin@123
- Email: careers@financehub.demo / Password: Admin@123

## 📁 Project Structure

```
findnaukri/
├── index.php                          # Homepage
├── jobs.php                           # Job search page
├── job-details.php                    # Individual job details
├── companies.php                      # Company directory
├── company-details.php                # Company profile
├── services.php                       # Services page
├── resources.php                      # Career resources
├── article.php                        # Individual article
├── about.php                          # About us
├── careers.php                        # FindNaukri careers
├── contact.php                        # Contact form
├── candidate-register.php             # Candidate registration
├── candidate-login.php                # Candidate login
├── employer-register.php              # Employer registration
├── employer-login.php                 # Employer login
├── admin-login.php                    # Admin login
├── forgot-password.php                # Password reset request
├── reset-password.php                 # Password reset form
├── logout.php                         # Logout handler
│
├── candidate/                         # Candidate dashboard
│   ├── dashboard.php
│   ├── profile.php
│   ├── edit-profile.php
│   ├── my-applications.php
│   ├── saved-jobs.php
│   ├── recommended-jobs.php
│   ├── job-alerts.php
│   ├── notifications.php
│   └── settings.php
│
├── employer/                          # Employer dashboard
│   ├── dashboard.php
│   ├── company-profile.php
│   ├── post-job.php
│   ├── manage-jobs.php
│   ├── edit-job.php
│   ├── applicants.php
│   ├── candidate-details.php
│   ├── interviews.php
│   ├── analytics.php
│   └── settings.php
│
├── admin/                             # Admin dashboard
│   ├── dashboard.php
│   ├── candidates.php
│   ├── employers.php
│   ├── employer-details.php
│   ├── jobs.php
│   ├── applications.php
│   ├── categories.php
│   ├── articles.php
│   ├── enquiries.php
│   ├── reports.php
│   ├── settings.php
│   └── audit-logs.php
│
├── includes/                          # Shared components
│   ├── config.php                     # Configuration
│   ├── database.php                   # Database connection
│   ├── auth.php                       # Authentication
│   ├── functions.php                  # Helper functions
│   ├── csrf.php                       # CSRF protection
│   ├── validation.php                 # Input validation
│   ├── upload.php                     # File upload handler
│   ├── notifications.php              # Notification system
│   ├── mailer.php                     # Email handling
│   ├── header.php                     # Page header
│   └── footer.php                     # Page footer
│
├── assets/                            # Static assets
│   ├── css/
│   │   ├── style.css                  # Main stylesheet
│   │   ├── dashboard.css              # Dashboard styles
│   │   └── responsive.css             # Responsive styles
│   ├── js/
│   │   ├── main.js                    # Main JavaScript
│   │   └── dashboard.js               # Dashboard JavaScript
│   └── images/                        # Images and logos
│
├── uploads/                           # User uploads
│   ├── resumes/                       # Resume files
│   ├── profiles/                      # Profile photos
│   └── company-logos/                 # Company logos
│
├── database/                          # Database files
│   ├── schema.sql                     # Database schema
│   └── seed.sql                       # Demo data
│
├── .env.example                       # Environment template
├── .htaccess                          # Apache configuration
└── README.md                          # This file
```

## 🔧 Configuration

### Environment Variables (.env)

```env
# Database Configuration
DB_HOST=localhost
DB_NAME=findnaukri_db
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4

# Site Configuration
SITE_NAME=FindNaukri.in
SITE_URL=http://localhost/findnaukri
SITE_TAGLINE=Connecting Talent with Opportunities
SITE_EMAIL=contact@findnaukri.in
SITE_PHONE=+91-XXXXXXXXXX
SITE_ADDRESS=Mumbai, Maharashtra, India

# Email Configuration (SMTP)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_NAME=FindNaukri.in
MAIL_FROM_ADDRESS=noreply@findnaukri.in

# Session Configuration
SESSION_TIMEOUT=3600

# File Upload Configuration
MAX_RESUME_SIZE=5242880
MAX_IMAGE_SIZE=2097152
UPLOAD_DIR=uploads/

# Security Configuration
CSRF_TOKEN_NAME=csrf_token
ENCRYPTION_KEY=your-secret-encryption-key-min-32-chars

# Development Mode
ENVIRONMENT=development
DEBUG=true
```

## 🧪 Testing Checklist

### Candidate Testing
- [ ] Register new candidate account
- [ ] Verify email (if production)
- [ ] Login to candidate dashboard
- [ ] Complete profile (personal info, education, experience, skills)
- [ ] Upload resume
- [ ] Search for jobs
- [ ] View job details
- [ ] Apply for a job
- [ ] Save/bookmark jobs
- [ ] View my applications
- [ ] Check application status
- [ ] View notifications
- [ ] Update profile settings
- [ ] Logout successfully

### Employer Testing
- [ ] Register new employer account
- [ ] Verify email (if production)
- [ ] Wait for admin verification (or auto-verify in dev)
- [ ] Login to employer dashboard
- [ ] Complete company profile
- [ ] Upload company logo
- [ ] Post a new job
- [ ] Edit existing job
- [ ] Close a job listing
- [ ] View applicants for jobs
- [ ] Review candidate profiles
- [ ] Shortlist candidates
- [ ] Update application status
- [ ] Schedule interview
- [ ] View analytics
- [ ] Logout successfully

### Admin Testing
- [ ] Login with admin credentials
- [ ] View dashboard statistics
- [ ] Review pending employer registrations
- [ ] Approve/reject employer accounts
- [ ] Review pending job postings
- [ ] Approve/reject jobs
- [ ] View all candidates
- [ ] View all employers
- [ ] View all applications
- [ ] Manage job categories
- [ ] Create/edit articles
- [ ] View contact enquiries
- [ ] View audit logs
- [ ] Update platform settings
- [ ] Logout successfully

### Security Testing
- [ ] SQL injection protection (prepared statements)
- [ ] XSS protection (output escaping)
- [ ] CSRF protection on forms
- [ ] Password hashing (password_hash)
- [ ] Session security (regeneration, timeout)
- [ ] File upload validation
- [ ] Role-based access control
- [ ] Unauthorized access prevention
- [ ] Secure password reset tokens

### Functionality Testing
- [ ] Job search with filters
- [ ] Job sorting (newest, salary)
- [ ] Pagination
- [ ] Email notifications (if configured)
- [ ] File uploads (resumes, images)
- [ ] Form validation
- [ ] Error handling
- [ ] Empty states
- [ ] Responsive design (mobile, tablet, desktop)

## 🔒 Security Features

- **SQL Injection Protection**: All database queries use PDO prepared statements
- **XSS Protection**: All output is escaped using htmlspecialchars()
- **CSRF Protection**: All state-changing forms include CSRF tokens
- **Password Security**: Passwords hashed using password_hash() (bcrypt)
- **Session Security**: Session ID regeneration after login, session timeout
- **File Upload Security**: File type validation, size limits, secure filename generation
- **Role-Based Access Control**: Protected pages check user roles
- **Audit Logging**: All administrative actions are logged
- **Secure Headers**: X-Content-Type-Options, X-Frame-Options, X-XSS-Protection

## 📊 Database Schema

The database consists of 22 tables:

- `users` - All user accounts
- `candidate_profiles` - Extended candidate information
- `candidate_education` - Education records
- `candidate_experience` - Work experience
- `candidate_skills` - Skills
- `candidate_projects` - Projects and achievements
- `candidate_resumes` - Resume uploads
- `employers` - Company profiles
- `job_categories` - Job categories
- `jobs` - Job postings
- `job_applications` - Applications
- `application_status_history` - Status change history
- `saved_jobs` - Bookmarked jobs
- `job_alerts` - Job alert subscriptions
- `interviews` - Interview schedules
- `notifications` - User notifications
- `contact_enquiries` - Contact form submissions
- `articles` - Career articles
- `job_reports` - Reported jobs
- `password_reset_tokens` - Password reset tokens
- `audit_logs` - Audit trail
- `platform_settings` - Configuration settings

See `database/schema.sql` for complete schema definition.

## 🎨 Design System

### Colors
- Primary Orange: #F58220
- Navy Blue: #173F6B
- White: #FFFFFF
- Light Gray: #F3F4F6
- Cream: #FFF6ED
- Dark Charcoal: #343434

### Typography
- Font Family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif
- Base Font Size: 16px
- Line Height: 1.6

### Components
- Cards with subtle shadows and rounded corners
- Buttons with hover effects and transitions
- Forms with validation styling
- Tables with responsive design
- Modals for confirmations
- Toast notifications
- Empty states with helpful messages

## 🐛 Troubleshooting

### Database Connection Failed
- Check MySQL service is running
- Verify database credentials in `.env`
- Ensure database exists and schema is imported

### File Upload Not Working
- Check upload directory permissions (755)
- Verify PHP upload limits in php.ini
- Check available disk space

### Email Not Sending
- Verify SMTP credentials in `.env`
- Check if mail server requires SSL/TLS
- Test SMTP connection separately

### Session Issues
- Check session save path is writable
- Verify session timeout setting
- Clear browser cookies

### 404 Errors
- Ensure `.htaccess` is present
- Check mod_rewrite is enabled in Apache
- Verify file paths are correct

## 📝 Development Notes

### Adding New Features
1. Update database schema if needed
2. Create PHP files following existing structure
3. Use existing helper functions from `includes/functions.php`
4. Follow security best practices (CSRF, validation, prepared statements)
5. Update CSS/JS in respective directories
6. Test thoroughly before deployment

### Code Style
- Follow PSR-12 coding standards
- Use meaningful variable names
- Add comments for complex logic
- Keep functions focused and small
- Use existing patterns and conventions

## 🚀 Deployment Checklist

- [ ] Change all default passwords
- [ ] Remove or secure demo data
- [ ] Update environment variables for production
- [ ] Configure SMTP for email
- [ ] Enable HTTPS (SSL certificate)
- [ ] Set proper file permissions
- [ ] Disable debug mode
- [ ] Configure backup strategy
- [ ] Set up monitoring
- [ ] Test all functionality
- [ ] Update contact information
- [ ] Review and update privacy policy
- [ ] Test payment integration (if applicable)

## 📄 License

This project is created for educational and demonstration purposes. Please ensure compliance with all applicable laws and regulations when using this in a production environment.

## 🤝 Contributing

This is a demonstration project. For production use, consider:
- Adding more comprehensive error handling
- Implementing full email notification system with PHPMailer
- Adding API endpoints for mobile apps
- Implementing caching for performance
- Adding more advanced search and filtering
- Implementing video interview integration
- Adding payment gateway for premium features

## 📞 Support

For issues, questions, or support:
- Email: contact@findnaukri.in
- Website: https://findnaukri.in

## 🙏 Acknowledgments

Inspired by:
- Apna.co
- Naukri.com
- CIEL HR

Built with:
- Bootstrap 5
- Font Awesome
- Chart.js
- PHP
- MySQL

---

**FindNaukri.in - Connecting Talent with Opportunities**
