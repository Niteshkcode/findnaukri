<?php
/**
 * FindNaukri.in - Candidate Registration
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
    redirect(SITE_URL . '/candidate/dashboard.php');
}

$pageTitle = 'Candidate Registration - FindNaukri.in';
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    
    // Sanitize input
    $data = sanitizeInput($_POST);
    
    // Validate
    $validation = validateRegistration($data, ROLE_CANDIDATE);
    if (!$validation['valid']) {
        $errors = $validation['errors'];
    } else {
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
                $data['full_name'],
                $data['email'],
                $data['phone'],
                $passwordHash,
                ROLE_CANDIDATE,
                STATUS_PENDING
            ];
            
            if (execute($query, $params)) {
                $userId = lastInsertId();
                
                // Create candidate profile
                $query = "INSERT INTO candidate_profiles (user_id, current_city, current_state, experience_level) VALUES (?, ?, ?, ?)";
                execute($query, [
                    $userId,
                    $data['city'] ?? null,
                    $data['state'] ?? null,
                    $data['experience_level'] ?? 'fresher'
                ]);
                
                // Log registration
                logAudit($userId, 'register', 'user', $userId);

                // Create notification
                createNotification($userId, 'Welcome to FindNaukri!', 'Thank you for registering. Please verify your email to activate your account.', 'system');

                // Send OTP verification email
                $otpResult = sendOTPEmailToUser($userId, $data['email'], $data['full_name'], 'registration');

                if ($otpResult['success']) {
                    $success = true;
                    setFlash('success', 'Registration successful! A verification code has been sent to your email. Please check your inbox and enter the code to verify your account.');
                    redirect(SITE_URL . '/verify-email.php?user_id=' . $userId . '&role=candidate');
                } else {
                    // Email failed but registration succeeded
                    $success = true;
                    setFlash('warning', 'Registration successful! However, we could not send the verification email. Please request a new verification code.');
                    redirect(SITE_URL . '/verify-email.php?user_id=' . $userId . '&role=candidate');
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
                    Start Your <span class="highlight">Career Journey</span>
                </h1>
                
                <!-- Description -->
                <p class="login-description">
                    Create your account and get access to thousands of job opportunities. 
                    Build your profile, apply to jobs, and connect with top employers.
                </p>
                
                <!-- Features -->
                <div class="login-features">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="feature-text">
                            <h3>Easy Registration</h3>
                            <p>Create your profile in minutes</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="feature-text">
                            <h3>Smart Job Matching</h3>
                            <p>Get jobs that match your skills</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="feature-text">
                            <h3>Instant Notifications</h3>
                            <p>Never miss an opportunity</p>
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
            <div class="login-card register-card">
                <div class="login-card-header">
                    <h2>Create Account</h2>
                    <p>Join thousands of job seekers today</p>
                </div>
                
                <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo htmlspecialchars($errors['general']); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="login-form register-form">
                    <?php echo getCsrfField(); ?>
                    
                    <div class="form-group">
                        <label for="full_name">Full Name *</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" 
                                   class="form-control <?php echo isset($errors['full_name']) ? 'is-invalid' : ''; ?>" 
                                   id="full_name" 
                                   name="full_name" 
                                   required
                                   placeholder="Enter your full name"
                                   value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                        </div>
                        <?php if (isset($errors['full_name'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['full_name']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
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
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" 
                                   class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                   id="email" 
                                   name="email" 
                                   required
                                   placeholder="Enter your email"
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
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
                        <small class="form-text">Min 8 characters, with uppercase, lowercase, number, and special character</small>
                    </div>

                    <div class="form-group">
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
                    
                    <div class="form-row">
                        <div class="form-group half-width">
                            <label for="city">Current City</label>
                            <div class="input-wrapper">
                                <i class="fas fa-map-marker-alt input-icon"></i>
                                <input type="text" 
                                       class="form-control" 
                                       id="city" 
                                       name="city"
                                       placeholder="City"
                                       value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group half-width">
                            <label for="state">State</label>
                            <div class="input-wrapper">
                                <i class="fas fa-map input-icon"></i>
                                <input type="text" 
                                       class="form-control" 
                                       id="state" 
                                       name="state"
                                       placeholder="State"
                                       value="<?php echo htmlspecialchars($_POST['state'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="experience_level">Experience Level</label>
                        <div class="input-wrapper">
                            <i class="fas fa-briefcase input-icon"></i>
                            <select class="form-control" id="experience_level" name="experience_level">
                                <option value="fresher" <?php echo (($_POST['experience_level'] ?? '') === 'fresher') ? 'selected' : ''; ?>>Fresher</option>
                                <option value="0-1" <?php echo (($_POST['experience_level'] ?? '') === '0-1') ? 'selected' : ''; ?>>0-1 Years</option>
                                <option value="1-3" <?php echo (($_POST['experience_level'] ?? '') === '1-3') ? 'selected' : ''; ?>>1-3 Years</option>
                                <option value="3-5" <?php echo (($_POST['experience_level'] ?? '') === '3-5') ? 'selected' : ''; ?>>3-5 Years</option>
                                <option value="5-10" <?php echo (($_POST['experience_level'] ?? '') === '5-10') ? 'selected' : ''; ?>>5-10 Years</option>
                                <option value="10+" <?php echo (($_POST['experience_level'] ?? '') === '10+') ? 'selected' : ''; ?>>10+ Years</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group terms-group">
                        <div class="remember-me">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        Create Account
                    </button>
                </form>
                
                <div class="register-link">
                    Already have an account? 
                    <a href="<?php echo SITE_URL; ?>/login.php?tab=candidate">Login here</a>
                </div>
                
                <div class="employer-link">
                    Are you an employer? 
                    <a href="<?php echo SITE_URL; ?>/employer-register.php">Register as Employer</a>
                </div>
                
                <!-- Security Note -->
                <div class="security-note">
                    <i class="fas fa-shield-alt"></i>
                    <span>Your privacy and data security is our priority. We never share your information with third parties.</span>
                </div>
            </div>
        </div>
    </div>

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
