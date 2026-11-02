<?php
/**
 * FindNaukri.in - Email Verification Page
 * OTP-based email verification for new registrations
 */

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/validation.php';
require_once 'includes/csrf.php';
require_once 'includes/otp.php';
require_once 'includes/mailer.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(SITE_URL . '/index.php');
}

$pageTitle = 'Email Verification - FindNaukri.in';
$error = '';
$success = false;

// Get user ID and role from URL
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$role = isset($_GET['role']) ? $_GET['role'] : 'candidate';

if (!$userId) {
    $error = 'Invalid verification request. Please register again.';
}

// Verify user exists and is not already verified
if (!$error) {
    $query = "SELECT id, full_name, email, role, status, email_verified_at FROM users WHERE id = ? LIMIT 1";
    $user = fetchOne($query, [$userId]);

    if (!$user) {
        $error = 'User not found. Please register again.';
    } elseif ($user['email_verified_at'] !== null) {
        $error = 'This email has already been verified. You can login to your account.';
    } elseif ($user['role'] !== $role) {
        $error = 'Invalid verification request.';
    }
}

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    requireCsrf();

    $otp = $_POST['otp'] ?? '';

    if (empty($otp)) {
        $error = 'Please enter the verification code';
    } elseif (!preg_match('/^\d{6}$/', $otp)) {
        $error = 'Please enter a valid 6-digit verification code';
    } else {
        // Validate OTP
        $result = validateOTP($userId, $otp, 'registration');

        if ($result['valid']) {
            // Mark email as verified
            $query = "UPDATE users SET email_verified_at = NOW(), status = ? WHERE id = ?";
            execute($query, [STATUS_ACTIVE, $userId]);

            // Log verification
            logAudit($userId, 'email_verified', 'user', $userId);

            // Send welcome email
            if ($role === ROLE_CANDIDATE) {
                sendWelcomeCandidateEmail($user['email'], $user['full_name'], SITE_URL . '/candidate/dashboard.php');
                $dashboardUrl = SITE_URL . '/candidate/dashboard.php';
            } else {
                sendWelcomeEmployerEmail($user['email'], $user['full_name'], SITE_URL . '/employer/dashboard.php');
                $dashboardUrl = SITE_URL . '/employer/dashboard.php';
            }

            // Auto login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['login_time'] = time();
            session_regenerate_id(true);

            $success = true;
            setFlash('success', 'Email verified successfully! Welcome to FindNaukri.in!');

            // Redirect to dashboard
            redirect($dashboardUrl);
        } else {
            $error = $result['message'];
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
                        <div class="mb-3">
                            <i class="fas fa-envelope-open-text fa-3x" style="color: #F58220;"></i>
                        </div>
                        <h2 class="card-title">Verify Your Email</h2>
                        <p class="text-muted">Enter the 6-digit code sent to your email</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!$error && !$success): ?>
                        <?php if (isset($user)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                A verification code has been sent to <strong><?php echo htmlspecialchars($user['email']); ?></strong>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" data-validate>
                            <?php echo getCsrfField(); ?>

                            <div class="mb-4">
                                <label for="otp" class="form-label text-center d-block">Verification Code</label>
                                <input type="text" class="form-control text-center" id="otp" name="otp"
                                       placeholder="000000" maxlength="6" pattern="\d{6}" required
                                       style="font-size: 24px; letter-spacing: 5px; font-weight: bold;">
                                <small class="text-muted d-block text-center mt-2">Enter the 6-digit code from your email</small>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">Verify Email</button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="mb-2">Didn't receive the code?</p>
                            <a href="<?php echo SITE_URL; ?>/resend-verification.php?user_id=<?php echo $userId; ?>&role=<?php echo $role; ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-redo me-2"></i>Resend Verification Code
                            </a>
                        </div>

                        <div class="text-center mt-3">
                            <a href="<?php echo SITE_URL; ?>/index.php" class="text-muted">
                                <i class="fas fa-arrow-left me-2"></i>Back to Home
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-format OTP input (add spaces after every 2 digits for readability)
document.addEventListener('DOMContentLoaded', function() {
    const otpInput = document.getElementById('otp');
    if (otpInput) {
        otpInput.addEventListener('input', function(e) {
            // Remove any non-digit characters
            this.value = this.value.replace(/\D/g, '');
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
