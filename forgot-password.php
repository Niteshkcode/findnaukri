<?php
/**
 * FindNaukri.in - Forgot Password
 */

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/validation.php';
require_once 'includes/csrf.php';

$pageTitle = 'Forgot Password - FindNaukri.in';
$error = '';
$success = false;
$role = $_GET['role'] ?? 'candidate';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    
    $email = sanitize($_POST['email'] ?? '');
    $role = sanitize($_POST['role'] ?? 'candidate');
    
    if (!validateEmail($email)) {
        $error = 'Please enter a valid email address';
    } else {
        // Check if user exists
        $query = "SELECT id, full_name FROM users WHERE email = ? AND role = ? LIMIT 1";
        $user = fetchOne($query, [$email, $role]);
        
        if ($user) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Delete any existing tokens for this user
            $query = "DELETE FROM password_reset_tokens WHERE user_id = ?";
            execute($query, [$user['id']]);
            
            // Insert new token
            $query = "INSERT INTO password_reset_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)";
            if (execute($query, [$user['id'], $tokenHash, $expiresAt])) {
                // In production, send email with reset link
                // For development, show the token
                $resetLink = SITE_URL . '/reset-password.php?token=' . $token . '&role=' . $role;
                
                if (getenv('ENVIRONMENT') === 'development') {
                    $success = true;
                    $message = "Password reset link: <a href='$resetLink'>$resetLink</a>";
                    setFlash('success', $message);
                } else {
                    // Send email (placeholder)
                    $emailSent = sendEmail($email, 'Password Reset Request', 
                        "Click the following link to reset your password: $resetLink");
                    
                    if ($emailSent) {
                        $success = true;
                        setFlash('success', 'Password reset link has been sent to your email address.');
                    } else {
                        $error = 'Failed to send email. Please try again.';
                    }
                }
            } else {
                $error = 'Failed to generate reset token. Please try again.';
            }
        } else {
            // Don't reveal if email exists or not for security
            $success = true;
            setFlash('success', 'If an account exists with this email, a password reset link has been sent.');
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
                        <h2 class="card-title">Forgot Password</h2>
                        <p class="text-muted">Enter your email to receive a password reset link</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!$success): ?>
                        <form method="POST" action="" data-validate>
                            <?php echo getCsrfField(); ?>
                            
                            <div class="mb-3">
                                <label for="role" class="form-label">Account Type</label>
                                <select class="form-select" id="role" name="role">
                                    <option value="candidate" <?php echo $role === 'candidate' ? 'selected' : ''; ?>>Candidate</option>
                                    <option value="employer" <?php echo $role === 'employer' ? 'selected' : ''; ?>>Employer</option>
                                    <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">Send Reset Link</button>
                            </div>
                        </form>
                    <?php endif; ?>
                    
                    <div class="text-center mt-4">
                        <p class="mb-0">Remember your password? 
                            <?php if ($role === 'candidate'): ?>
                                <a href="<?php echo SITE_URL; ?>/candidate-login.php">Login here</a>
                            <?php elseif ($role === 'employer'): ?>
                                <a href="<?php echo SITE_URL; ?>/employer-login.php">Login here</a>
                            <?php else: ?>
                                <a href="<?php echo SITE_URL; ?>/admin-login.php">Login here</a>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
