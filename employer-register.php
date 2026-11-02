<?php
/**
 * FindNaukri.in - Employer Registration
 * Redesigned with two-column layout matching login page
 */

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/validation.php';
require_once 'includes/csrf.php';
require_once 'includes/otp.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(SITE_URL . '/employer/dashboard.php');
}

$pageTitle = 'Employer Registration - FindNaukri.in';
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    
    // Sanitize input
    $data = sanitizeInput($_POST);
    
    // Validate
    $validation = validateRegistration($data, ROLE_EMPLOYER);
    if (!$validation['valid']) {
        $errors = $validation['errors'];
    } else {
        // Map recruiter_name to full_name for database
        $data['full_name'] = $data['recruiter_name'];

        // Check if email already exists
        $query = "SELECT id FROM users WHERE email = ? LIMIT 1";
        $existing = fetchOne($query, [$data['email']]);
        
        if ($existing) {
            $errors['email'] = 'This email is already registered';
        } else {
            // Hash password
            $passwordHash = hashPassword($data['password']);
            
            // Insert user
            $query = "INSERT INTO users (full_name, email, phone, password_hash, role, status) VALUES (?, ?, ?, ?, ?, ?)";
            $params = [
                $data['recruiter_name'],
                $data['email'],
                $data['phone'],
                $passwordHash,
                ROLE_EMPLOYER,
                STATUS_PENDING
            ];
            
            if (execute($query, $params)) {
                $userId = lastInsertId();
                
                // Generate company slug
                $companySlug = generateSlug($data['company_name']);
                
                // Ensure unique slug
                $slugQuery = "SELECT id FROM employers WHERE company_slug = ? LIMIT 1";
                $counter = 1;
                while (fetchOne($slugQuery, [$companySlug])) {
                    $companySlug = generateSlug($data['company_name']) . '-' . $counter;
                    $counter++;
                }
                
                // Create employer profile
                $query = "INSERT INTO employers (user_id, company_name, company_slug, industry, website, description, city, state, verification_status) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                execute($query, [
                    $userId,
                    $data['company_name'],
                    $companySlug,
                    $data['industry'] ?? null,
                    $data['website'] ?? null,
                    $data['description'] ?? null,
                    $data['city'] ?? null,
                    $data['state'] ?? null,
                    'pending'
                ]);
                
                // Log registration
                logAudit($userId, 'register', 'user', $userId);

                // Create notification for admin
                $adminQuery = "SELECT id FROM users WHERE role = ? AND status = ? LIMIT 1";
                $admin = fetchOne($adminQuery, [ROLE_ADMIN, STATUS_ACTIVE]);

                if ($admin) {
                    createNotification($admin['id'], 'New Employer Registration',
                        'A new employer has registered: ' . $data['company_name'], 'system', $userId);
                }

                // Send OTP verification email
                $otpResult = sendOTPEmailToUser($userId, $data['email'], $data['recruiter_name'], 'registration');

                if ($otpResult['success']) {
                    $success = true;
                    setFlash('success', 'Registration successful! A verification code has been sent to your email. Please verify your account to continue.');
                    redirect(SITE_URL . '/verify-email.php?user_id=' . $userId . '&role=employer');
                } else {
                    // Email failed but registration succeeded
                    $success = true;
                    setFlash('warning', 'Registration successful! However, we could not send the verification email. Please request a new verification code.');
                    redirect(SITE_URL . '/verify-email.php?user_id=' . $userId . '&role=employer');
                }
            } else {
                $errors['general'] = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/login.css">
    <link rel="stylesheet" href="/assets/css/login.css">
</head>
<body class="login-page">
    <div class="login-container">
        <!-- Left Column - Promotional -->
        <div class="login-left">
            <div class="login-left-content">
                <!-- Logo -->
                <div class="login-logo">
                    <a href="<?php echo SITE_URL; ?>/index.php">
                        <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="FindNaukri.in Logo">
                    </a>
                </div>
                
                <!-- Headline -->
                <h1 class="login-headline">
                    Find Top <span class="highlight">Talent</span> Today
                </h1>
                
                <!-- Description -->
                <p class="login-description">
                    Register your company and connect with thousands of qualified candidates. 
                    Post jobs, manage applications, and build your dream team.
                </p>
                
                <!-- Features -->
                <div class="login-features">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="feature-text">
                            <h3>Company Profile</h3>
                            <p>Showcase your brand to candidates</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="feature-text">
                            <h3>Analytics Dashboard</h3>
                            <p>Track your hiring performance</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div class="feature-text">
                            <h3>Easy Management</h3>
                            <p>Streamline your hiring process</p>
                        </div>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="login-stats">
                    <div class="stat-item">
                        <div class="stat-number">10K+</div>
                        <div class="stat-label">Jobs Available</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">5K+</div>
                        <div class="stat-label">Companies</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">50K+</div>
                        <div class="stat-label">Happy Users</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Registration Form -->
        <div class="login-right">
            <div class="login-card register-card employer-register-card">
                <div class="login-card-header">
                    <h2>Register Company</h2>
                    <p>Start hiring the best talent today</p>
                </div>
                
                <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo htmlspecialchars($errors['general']); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="login-form register-form">
                    <?php echo getCsrfField(); ?>
                    
                    <h3 class="section-divider">Company Information</h3>
                    
                    <div class="form-group">
                        <label for="company_name">Company Name *</label>
                        <div class="input-wrapper">
                            <i class="fas fa-building input-icon"></i>
                            <input type="text" 
                                   class="form-control <?php echo isset($errors['company_name']) ? 'is-invalid' : ''; ?>" 
                                   id="company_name" 
                                   name="company_name" 
                                   required
                                   placeholder="Enter company name"
                                   value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>">
                        </div>
                        <?php if (isset($errors['company_name'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['company_name']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="industry">Industry *</label>
                        <div class="input-wrapper">
                            <i class="fas fa-industry input-icon"></i>
                            <select class="form-control <?php echo isset($errors['industry']) ? 'is-invalid' : ''; ?>" 
                                    id="industry" 
                                    name="industry" 
                                    required>
                                <option value="">Select Industry</option>
                                <option value="Information Technology" <?php echo (($_POST['industry'] ?? '') === 'Information Technology') ? 'selected' : ''; ?>>Information Technology</option>
                                <option value="Manufacturing" <?php echo (($_POST['industry'] ?? '') === 'Manufacturing') ? 'selected' : ''; ?>>Manufacturing</option>
                                <option value="Banking & Finance" <?php echo (($_POST['industry'] ?? '') === 'Banking & Finance') ? 'selected' : ''; ?>>Banking & Finance</option>
                                <option value="Healthcare" <?php echo (($_POST['industry'] ?? '') === 'Healthcare') ? 'selected' : ''; ?>>Healthcare</option>
                                <option value="Education" <?php echo (($_POST['industry'] ?? '') === 'Education') ? 'selected' : ''; ?>>Education</option>
                                <option value="Retail" <?php echo (($_POST['industry'] ?? '') === 'Retail') ? 'selected' : ''; ?>>Retail</option>
                                <option value="Consulting" <?php echo (($_POST['industry'] ?? '') === 'Consulting') ? 'selected' : ''; ?>>Consulting</option>
                                <option value="Other" <?php echo (($_POST['industry'] ?? '') === 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <?php if (isset($errors['industry'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['industry']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group half-width">
                            <label for="city">City *</label>
                            <div class="input-wrapper">
                                <i class="fas fa-map-marker-alt input-icon"></i>
                                <input type="text" 
                                       class="form-control" 
                                       id="city" 
                                       name="city" 
                                       required
                                       placeholder="City"
                                       value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group half-width">
                            <label for="state">State *</label>
                            <div class="input-wrapper">
                                <i class="fas fa-map input-icon"></i>
                                <input type="text" 
                                       class="form-control" 
                                       id="state" 
                                       name="state" 
                                       required
                                       placeholder="State"
                                       value="<?php echo htmlspecialchars($_POST['state'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="website">Company Website</label>
                        <div class="input-wrapper">
                            <i class="fas fa-globe input-icon"></i>
                            <input type="url" 
                                   class="form-control" 
                                   id="website" 
                                   name="website"
                                   placeholder="https://example.com"
                                   value="<?php echo htmlspecialchars($_POST['website'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Company Description</label>
                        <div class="input-wrapper">
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      placeholder="Brief description of your company"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <h3 class="section-divider">Recruiter Information</h3>
                    
                    <div class="form-group">
                        <label for="recruiter_name">Recruiter Name *</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" 
                                   class="form-control <?php echo isset($errors['recruiter_name']) ? 'is-invalid' : ''; ?>"
                                   id="recruiter_name" 
                                   name="recruiter_name" 
                                   required
                                   placeholder="Enter recruiter name"
                                   value="<?php echo htmlspecialchars($_POST['recruiter_name'] ?? ''); ?>"
                                   pattern="[a-zA-Z\s\-\'\.]{2,100}"
                                   title="Name can contain letters, spaces, hyphens, and apostrophes">
                        </div>
                        <?php if (isset($errors['recruiter_name'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['recruiter_name']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group half-width">
                            <label for="email">Official Email *</label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" 
                                       class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                       id="email" 
                                       name="email" 
                                       required
                                       placeholder="Enter official email"
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group half-width">
                            <label for="phone">Mobile Number *</label>
                            <div class="input-wrapper">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="tel" 
                                       class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" 
                                       id="phone" 
                                       name="phone" 
                                       required
                                       placeholder="10-digit mobile number"
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>
                            <?php if (isset($errors['phone'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['phone']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group half-width">
                            <label for="password">Password *</label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" 
                                       class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>"
                                       id="password" 
                                       name="password" 
                                       required
                                       minlength="8"
                                       placeholder="Create a strong password">
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="passwordIcon"></i>
                                </button>
                            </div>
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['password']); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group half-width">
                            <label for="confirm_password">Confirm Password *</label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" 
                                       class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>"
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       required
                                       minlength="8"
                                       placeholder="Confirm your password">
                                <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                    <i class="fas fa-eye" id="confirmPasswordIcon"></i>
                                </button>
                            </div>
                            <?php if (isset($errors['confirm_password'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['confirm_password']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group terms-group">
                        <div class="remember-me">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        Register Company
                    </button>
                </form>
                
                <div class="register-link">
                    Already have an account? 
                    <a href="<?php echo SITE_URL; ?>/login.php?tab=employer">Login here</a>
                </div>
                
                <div class="employer-link">
                    Are you a job seeker? 
                    <a href="<?php echo SITE_URL; ?>/candidate-register.php">Register as Candidate</a>
                </div>
                
                <!-- Security Note -->
                <div class="security-note">
                    <i class="fas fa-shield-alt"></i>
                    <span>Your privacy and data security is our priority. We never share your information with third parties.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const iconId = fieldId === 'password' ? 'passwordIcon' : 'confirmPasswordIcon';
            const passwordIcon = document.getElementById(iconId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
