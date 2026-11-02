<?php
/**
 * FindNaukri.in - Contact Page
 */

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'includes/validation.php';
require_once 'includes/csrf.php';

$pageTitle = 'Contact Us - FindNaukri.in';
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    
    // Sanitize input
    $data = sanitizeInput($_POST);
    
    // Validate
    $validation = validateRequired($data, ['name', 'email', 'subject', 'message']);
    if (!$validation['valid']) {
        $errors = $validation['errors'];
    } else {
        if (!validateEmail($data['email'])) {
            $errors['email'] = 'Please enter a valid email address';
        }
        
        if (empty($errors)) {
            // Insert enquiry
            $query = "INSERT INTO contact_enquiries (name, email, phone, subject, message, enquiry_type, status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $params = [
                $data['name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['subject'],
                $data['message'],
                $data['enquiry_type'] ?? 'general',
                'new'
            ];
            
            if (execute($query, $params)) {
                $success = true;
                setFlash('success', 'Thank you for contacting us. We will get back to you soon.');
                
                // Notify admin
                $adminQuery = "SELECT id FROM users WHERE role = ? AND status = ? LIMIT 1";
                $admin = fetchOne($adminQuery, [ROLE_ADMIN, STATUS_ACTIVE]);
                
                if ($admin) {
                    createNotification($admin['id'], 'New Contact Enquiry', 
                        'New enquiry from ' . $data['name'] . ': ' . $data['subject'], 'system');
                }
            } else {
                $errors['general'] = 'Failed to submit enquiry. Please try again.';
            }
        }
    }
}

require_once 'includes/header.php';
?>

<!-- Page Header -->
<section class="section" style="background-color: var(--light-gray); color: var(--dark-charcoal); padding: 2rem 0;">
    <div class="container">
        <h1 class="mb-2">Contact Us</h1>
        <p class="mb-0" style="color: var(--text-gray);">We're here to help. Get in touch with us.</p>
    </div>
</section>

<!-- Contact Section -->
<section class="section">
    <div class="container">
        <div class="row">
            <!-- Contact Info -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="mb-4">Contact Information</h5>
                        
                        <div class="mb-4">
                            <div class="d-flex align-items-start mb-3">
                                <div class="me-3">
                                    <i class="fas fa-map-marker-alt fa-lg text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Address</h6>
                                    <p class="text-muted mb-0"><?php echo SITE_ADDRESS; ?></p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start mb-3">
                                <div class="me-3">
                                    <i class="fas fa-envelope fa-lg text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Email</h6>
                                    <p class="text-muted mb-0"><?php echo SITE_EMAIL; ?></p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start mb-3">
                                <div class="me-3">
                                    <i class="fas fa-phone fa-lg text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Phone</h6>
                                    <p class="text-muted mb-0"><?php echo SITE_PHONE; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3">Support Channels</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="#" class="text-decoration-none">
                                    <i class="fas fa-user me-2 text-primary"></i>Candidate Support
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-decoration-none">
                                    <i class="fas fa-building me-2 text-primary"></i>Employer Support
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-decoration-none">
                                    <i class="fas fa-briefcase me-2 text-primary"></i>Business Enquiry
                                </a>
                            </li>
                        </ul>
                        
                        <hr>
                        
                        <h5 class="mb-3">Follow Us</h5>
                        <div class="social-links">
                            <a href="#" class="me-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                            <a href="#" class="me-3"><i class="fab fa-twitter fa-lg"></i></a>
                            <a href="#" class="me-3"><i class="fab fa-linkedin-in fa-lg"></i></a>
                            <a href="#" class="me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-4">Send us a Message</h5>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                Thank you for contacting us. We will get back to you soon.
                            </div>
                        <?php else: ?>
                            <?php if (!empty($errors['general'])): ?>
                                <div class="alert alert-danger">
                                    <?php echo htmlspecialchars($errors['general']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="" data-validate>
                                <?php echo getCsrfField(); ?>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                               id="name" name="name" required
                                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                                        <?php if (isset($errors['name'])): ?>
                                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['name']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                               id="email" name="email" required
                                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                        <?php if (isset($errors['email'])): ?>
                                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                               placeholder="10-digit mobile number">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="enquiry_type" class="form-label">Enquiry Type</label>
                                        <select class="form-select" id="enquiry_type" name="enquiry_type">
                                            <option value="general" <?php echo (($_POST['enquiry_type'] ?? '') === 'general' ? 'selected' : ''); ?>>General Enquiry</option>
                                            <option value="candidate_support" <?php echo (($_POST['enquiry_type'] ?? '') === 'candidate_support' ? 'selected' : ''; ?>>Candidate Support</option>
                                            <option value="employer_support" <?php echo (($_POST['enquiry_type'] ?? '') === 'employer_support' ? 'selected' : ''; ?>>Employer Support</option>
                                            <option value="business" <?php echo (($_POST['enquiry_type'] ?? '') === 'business' ? 'selected' : ''; ?>>Business Enquiry</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject *</label>
                                    <input type="text" class="form-control <?php echo isset($errors['subject']) ? 'is-invalid' : ''; ?>" 
                                           id="subject" name="subject" required
                                           value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
                                    <?php if (isset($errors['subject'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['subject']); ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message *</label>
                                    <textarea class="form-control <?php echo isset($errors['message']) ? 'is-invalid' : ''; ?>" 
                                              id="message" name="message" rows="5" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                                    <?php if (isset($errors['message'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['message']); ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">Send Message</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="section" style="background-color: var(--cream);">
    <div class="container">
        <h2 class="section-title text-center mb-4">Frequently Asked Questions</h2>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                How do I create a candidate account?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Click on "Register" on the homepage, select "Candidate", fill in your details, and verify your email address. You can then complete your profile and start applying for jobs.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                How do employers post jobs?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Register as an employer, complete your company profile, and wait for admin verification. Once verified, you can post jobs from your employer dashboard.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Is FindNaukri free for job seekers?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, FindNaukri is completely free for job seekers. You can create a profile, search for jobs, apply to positions, and track your applications at no cost.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                How do I report a suspicious job posting?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                If you come across a suspicious job posting, click the "Report Job" button on the job details page. Our team will review the report and take appropriate action.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
