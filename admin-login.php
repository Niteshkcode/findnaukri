<?php
/**
 * FindNaukri.in - Admin Login
 */

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/validation.php';
require_once 'includes/csrf.php';

// Redirect if already logged in
if (isLoggedIn() && isAdmin()) {
    redirect(SITE_URL . '/admin/dashboard.php');
}

$pageTitle = 'Admin Login - FindNaukri.in';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $result = loginUser($email, $password, ROLE_ADMIN);
    
    if ($result['success']) {
        $redirect = $_SESSION['redirect_after_login'] ?? SITE_URL . '/admin/dashboard.php';
        unset($_SESSION['redirect_after_login']);
        redirect($redirect);
    } else {
        $error = $result['message'];
    }
}

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-user-shield fa-3x text-primary"></i>
                        </div>
                        <h2 class="card-title">Admin Login</h2>
                        <p class="text-muted">Secure admin access</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['timeout'])): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-clock me-2"></i>
                            Your session has expired. Please login again.
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" data-validate>
                        <?php echo getCsrfField(); ?>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Admin Email</label>
                            <input type="email" class="form-control" id="email" name="email" required
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="password-input-wrapper" style="position: relative;">
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-lock me-2"></i>Login
                            </button>
                        </div>
                        
                        <div class="text-center mb-3">
                            <a href="<?php echo SITE_URL; ?>/forgot-password.php?role=admin">Forgot Password?</a>
                        </div>
                    </form>
                    
                    <div class="alert alert-info mt-4">
                        <small>
                            <i class="fas fa-info-circle me-2"></i>
                            This is a restricted area. Unauthorized access is prohibited and will be logged.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
