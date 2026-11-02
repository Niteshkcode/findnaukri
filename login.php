<?php
/**
 * FindNaukri.in - Unified Login Page
 * Two-column design with Job Seeker and Employer tabs
 */

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/validation.php';
require_once 'includes/csrf.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isCandidate()) {
        redirect(SITE_URL . '/candidate/dashboard.php');
    } elseif (isEmployer()) {
        redirect(SITE_URL . '/employer/dashboard.php');
    } elseif (isAdmin()) {
        redirect(SITE_URL . '/admin/dashboard.php');
    }
}

$pageTitle = 'Login - FindNaukri.in';
$error = '';
$success = '';
$activeTab = isset($_GET['tab']) && $_GET['tab'] === 'employer' ? 'employer' : 'candidate';
$includeLoginCSS = true;
$bodyClass = 'login-page';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? ROLE_CANDIDATE;
    
    $result = loginUser($email, $password, $role);
    
    if ($result['success']) {
        // Check for redirect URL
        $redirect = $_SESSION['redirect_after_login'] ?? null;
        unset($_SESSION['redirect_after_login']);

        if ($redirect) {
            redirect($redirect);
        } else {
            // Redirect based on role
            if ($role === ROLE_CANDIDATE) {
                redirect(SITE_URL . '/candidate/dashboard.php');
            } elseif ($role === ROLE_EMPLOYER) {
                // Check if employer is verified
                if (!isEmployerVerified(getCurrentUserId())) {
                    logoutUser();
                    $error = 'Your account is pending verification. Please wait for admin approval.';
                } else {
                    redirect(SITE_URL . '/employer/dashboard.php');
                }
            }
        }
    } else {
        $error = $result['message'];
        $activeTab = $role;

        // If error is about email verification, add resend link
        if (strpos($error, 'verify your email') !== false) {
            $query = "SELECT id FROM users WHERE email = ? AND role = ? LIMIT 1";
            $user = fetchOne($query, [$email, $role]);
            if ($user) {
                $roleParam = $role === ROLE_CANDIDATE ? 'candidate' : 'employer';
                $error .= ' <a href="' . SITE_URL . '/resend-verification.php?user_id=' . $user['id'] . '&role=' . $roleParam . '" style="color: #F58220;">Resend verification email</a>';
            }
        }
    }
}

// Check for success flash message
$flash = getFlash();
if ($flash && $flash['type'] === 'success') {
    $success = $flash['message'];
}

require_once 'includes/header.php';
?>
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
                    Your Next Opportunity <span class="highlight">Starts Here</span>
                </h1>
                
                <!-- Description -->
                <p class="login-description">
                    Find your dream job with thousands of opportunities across various industries. 
                    Your career journey begins with a single click.
                </p>
                
                <!-- Features -->
                <div class="login-features">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="feature-text">
                            <h3>Latest Job Openings</h3>
                            <p>Updated daily with new opportunities</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="feature-text">
                            <h3>For Everyone</h3>
                            <p>Freshers to experienced professionals</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="feature-text">
                            <h3>Secure & Trusted</h3>
                            <p>Your data is always protected</p>
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
        
        <!-- Right Column - Login Form -->
        <div class="login-right">
            <div class="login-card">
                <div class="login-card-header">
                    <h2>Welcome Back!</h2>
                    <p>Login to your account and continue your journey</p>
                </div>
                
                <!-- Tabs -->
                <div class="login-tabs">
                    <button class="tab-btn <?php echo $activeTab === 'candidate' ? 'active' : ''; ?>" 
                            onclick="switchTab('candidate')">
                        Job Seeker
                    </button>
                    <button class="tab-btn <?php echo $activeTab === 'employer' ? 'active' : ''; ?>" 
                            onclick="switchTab('employer')">
                        Employer
                    </button>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['timeout'])): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-clock me-2"></i>
                        Your session has expired. Please login again.
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" id="loginForm" class="login-form">
                    <?php echo getCsrfField(); ?>
                    
                    <input type="hidden" name="role" id="roleInput" value="<?php echo $activeTab === 'employer' ? ROLE_EMPLOYER : ROLE_CANDIDATE; ?>">
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   required
                                   placeholder="Enter your email"
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   required
                                   placeholder="Enter your password">
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-options">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        <a href="<?php echo SITE_URL; ?>/forgot-password.php?role=<?php echo $activeTab; ?>" class="forgot-password">
                            Forgot Password?
                        </a>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        Login
                    </button>
                </form>
                
                <div class="register-link">
                    Don't have an account? 
                    <a href="<?php echo $activeTab === 'employer' ? SITE_URL . '/employer-register.php' : SITE_URL . '/candidate-register.php'; ?>">
                        Register Now
                    </a>
                </div>
                
                <!-- Security Note -->
                <div class="security-note">
                    <i class="fas fa-shield-alt"></i>
                    <span>Your privacy and data security is our priority. We never share your information with third parties.</span>
                </div>
                
                <!-- Support Link -->
                <div class="support-link">
                    <a href="<?php echo SITE_URL; ?>/contact.php">Need help? Contact Support</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function switchTab(tab) {
            // Update active tab styling
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Update role input
            document.getElementById('roleInput').value = tab === 'employer' ? '<?php echo ROLE_EMPLOYER; ?>' : '<?php echo ROLE_CANDIDATE; ?>';
            
            // Update forgot password link
            const forgotLink = document.querySelector('.forgot-password');
            forgotLink.href = '<?php echo SITE_URL; ?>/forgot-password.php?role=' + tab;
            
            // Update register link
            const registerLink = document.querySelector('.register-link a');
            registerLink.href = tab === 'employer' 
                ? '<?php echo SITE_URL; ?>/employer-register.php' 
                : '<?php echo SITE_URL; ?>/candidate-register.php';
        }
        
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
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

<?php require_once 'includes/footer.php'; ?>
