<?php
/**
 * FindNaukri.in - Reset Password
 */

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/validation.php';
require_once 'includes/csrf.php';

$pageTitle = 'Reset Password - FindNaukri.in';
$error = '';
$success = false;
$token = $_GET['token'] ?? '';
$role = $_GET['role'] ?? 'candidate';

// Validate token
if (!$token) {
    $error = 'Invalid reset token';
} else {
    $tokenHash = hash('sha256', $token);
    $query = "SELECT prt.user_id, u.email, u.role 
              FROM password_reset_tokens prt
              JOIN users u ON prt.user_id = u.id
              WHERE prt.token_hash = ? AND prt.expires_at > NOW() AND prt.used_at IS NULL
              LIMIT 1";
    $resetRequest = fetchOne($query, [$tokenHash]);
    
    if (!$resetRequest) {
        $error = 'Invalid or expired reset token';
    } elseif ($resetRequest['role'] !== $role) {
        $error = 'Invalid account type for this token';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    requireCsrf();
    
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    $passwordValidation = validatePassword($password);
    if (!$passwordValidation['valid']) {
        $error = $passwordValidation['message'];
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        // Update password
        $passwordHash = hashPassword($password);
        $query = "UPDATE users SET password_hash = ? WHERE id = ?";
        
        if (execute($query, [$passwordHash, $resetRequest['user_id']])) {
            // Mark token as used
            $query = "UPDATE password_reset_tokens SET used_at = NOW() WHERE token_hash = ?";
            execute($query, [$tokenHash]);
            
            // Log password reset
            logAudit($resetRequest['user_id'], 'password_reset', 'user', $resetRequest['user_id']);
            
            $success = true;
            setFlash('success', 'Password has been reset successfully. Please login with your new password.');
            
            // Redirect to login
            if ($role === 'candidate') {
                redirect(SITE_URL . '/candidate-login.php');
            } elseif ($role === 'employer') {
                redirect(SITE_URL . '/employer-login.php');
            } else {
                redirect(SITE_URL . '/admin-login.php');
            }
        } else {
            $error = 'Failed to reset password. Please try again.';
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h2 class="card-title">Reset Password</h2>
                        <p class="text-muted">Enter your new password</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                        <div class="text-center">
                            <a href="<?php echo SITE_URL; ?>/forgot-password.php?role=<?php echo $role; ?>" class="btn btn-primary">Request New Reset Link</a>
                        </div>
                    <?php elseif (!$success): ?>
                        <form method="POST" action="" data-validate>
                            <?php echo getCsrfField(); ?>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password *</label>
                                <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                       id="password" name="password" required
                                       minlength="8">
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['password']); ?></div>
                                <?php endif; ?>
                                <small class="text-muted">Min 8 characters, with uppercase, lowercase, number, and special character</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password *</label>
                                <input type="password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
                                       id="confirm_password" name="confirm_password" required
                                       minlength="8">
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['confirm_password']); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Reset Password</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
