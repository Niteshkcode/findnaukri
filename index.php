<?php
/**
 * FindNaukri.in - Homepage
 */

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'includes/upload.php';
require_once 'includes/auth.php';
require_once 'includes/validation.php';
require_once 'includes/csrf.php';

$pageTitle = 'Home - FindNaukri.in';

// Get featured jobs
$query = "SELECT j.*, e.company_name, e.logo_path, c.name as category_name
          FROM jobs j
          JOIN employers e ON j.employer_id = e.id
          JOIN job_categories c ON j.category_id = c.id
          WHERE j.approval_status = 'approved' AND j.job_status = 'active'
          AND (j.deadline IS NULL OR j.deadline >= CURDATE())
          ORDER BY j.posted_at DESC
          LIMIT 6";
$featuredJobs = fetchAll($query);

// Get job categories
$query = "SELECT * FROM job_categories WHERE status = 'active' ORDER BY name LIMIT 10";
$categories = fetchAll($query);

// Get top companies with their latest job vacancies
$query = "SELECT j.*, e.company_name, e.logo_path, c.name as category_name
          FROM jobs j
          JOIN employers e ON j.employer_id = e.id
          JOIN job_categories c ON j.category_id = c.id
          WHERE j.approval_status = 'approved' AND j.job_status = 'active'
          AND (j.deadline IS NULL OR j.deadline >= CURDATE())
          AND e.verification_status = 'verified'
          ORDER BY j.posted_at DESC
          LIMIT 6";
$topCompanyJobs = fetchAll($query);

// Handle contact form submission
$contactErrors = [];
$contactSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    requireCsrf();
    $data = sanitizeInput($_POST);

    // Validate contact form
    if (empty($data['first_name'])) {
        $contactErrors['first_name'] = 'First name is required';
    }
    if (empty($data['last_name'])) {
        $contactErrors['last_name'] = 'Last name is required';
    }
    if (!validateEmail($data['email'] ?? '')) {
        $contactErrors['email'] = 'Valid email is required';
    }
    if (!validatePhone($data['phone'] ?? '')) {
        $contactErrors['phone'] = 'Valid phone number is required';
    }
    if (empty($data['resource_type'])) {
        $contactErrors['resource_type'] = 'Resource type is required';
    }
    if (empty($data['resource_count'])) {
        $contactErrors['resource_count'] = 'Resource count is required';
    }

    if (empty($contactErrors)) {
        // Insert into contact_enquiries
        $query = "INSERT INTO contact_enquiries (name, email, phone, subject, message, status, created_at)
                  VALUES (?, ?, ?, ?, ?, 'new', NOW())";
        $fullName = $data['first_name'] . ' ' . $data['last_name'];
        $subject = 'Resource Enquiry - ' . $data['resource_type'] . ' (' . $data['resource_count'] . ')';
        $message = 'Resource Type: ' . $data['resource_type'] . "\n" .
                   'Resources Needed: ' . $data['resource_count'] . "\n" .
                   'Email: ' . $data['email'] . "\n" .
                   'Phone: ' . $data['phone'];

        if (execute($query, [$fullName, $data['email'], $data['phone'], $subject, $message])) {
            $contactSuccess = true;
            setFlash('success', 'Your enquiry has been submitted successfully. We will contact you soon.');
        } else {
            $contactErrors['general'] = 'Failed to submit enquiry. Please try again.';
        }
    }
}

require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container hero-content">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="hero-title">Find the Right Job. Build Your Future.</h1>
                <p class="hero-subtitle">FindNaukri connects talented professionals with top employers across India. Your dream job is just a search away.</p>

                <!-- Search Bar -->
                <div class="search-container mt-4">
                    <form class="search-form" action="<?php echo SITE_URL; ?>/jobs.php" method="GET">
                        <input type="text" class="search-input" name="keyword" placeholder="Job title, skills, or company">
                        <input type="text" class="search-input" name="location" placeholder="City or location">
                        <select class="search-input" name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="search-btn">Search Jobs</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-5 mt-4 mt-lg-0">
                <div class="hero-image">
                    <img src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=600&h=400&fit=crop"
                         alt="Professional Team"
                         class="img-fluid rounded-4 shadow">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Us Section -->
<section class="section contact-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="contact-image">
                    <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop"
                         alt="Professional Team"
                         class="img-fluid rounded-4 shadow">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="contact-form-wrapper">
                    <h2 class="contact-title">Get In Touch</h2>
                    <p class="contact-subtitle">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>

                    <?php if ($contactSuccess): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Your enquiry has been submitted successfully. We will contact you soon.
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($contactErrors['general'])): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($contactErrors['general']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="contact-form">
                        <?php echo getCsrfField(); ?>
                        <input type="hidden" name="contact_submit" value="1">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control <?php echo isset($contactErrors['first_name']) ? 'is-invalid' : ''; ?>"
                                       id="first_name" name="first_name" required
                                       value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                                <?php if (isset($contactErrors['first_name'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($contactErrors['first_name']); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control <?php echo isset($contactErrors['last_name']) ? 'is-invalid' : ''; ?>"
                                       id="last_name" name="last_name" required
                                       value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                                <?php if (isset($contactErrors['last_name'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($contactErrors['last_name']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Work Email *</label>
                            <input type="email" class="form-control <?php echo isset($contactErrors['email']) ? 'is-invalid' : ''; ?>"
                                   id="email" name="email" required
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            <?php if (isset($contactErrors['email'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($contactErrors['email']); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control <?php echo isset($contactErrors['phone']) ? 'is-invalid' : ''; ?>"
                                   id="phone" name="phone" required
                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                   placeholder="10-digit mobile number">
                            <?php if (isset($contactErrors['phone'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($contactErrors['phone']); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="resource_type" class="form-label">Select The Resources Type *</label>
                                <select class="form-select <?php echo isset($contactErrors['resource_type']) ? 'is-invalid' : ''; ?>"
                                        id="resource_type" name="resource_type" required>
                                    <option value="">Select Type</option>
                                    <option value="Top Level Management" <?php echo (($_POST['resource_type'] ?? '') === 'Top Level Management') ? 'selected' : ''; ?>>Top Level Management</option>
                                    <option value="Middle Management" <?php echo (($_POST['resource_type'] ?? '') === 'Middle Management') ? 'selected' : ''; ?>>Middle Management</option>
                                    <option value="Technical Team" <?php echo (($_POST['resource_type'] ?? '') === 'Technical Team') ? 'selected' : ''; ?>>Technical Team</option>
                                    <option value="Support Staff" <?php echo (($_POST['resource_type'] ?? '') === 'Support Staff') ? 'selected' : ''; ?>>Support Staff</option>
                                    <option value="Sales Team" <?php echo (($_POST['resource_type'] ?? '') === 'Sales Team') ? 'selected' : ''; ?>>Sales Team</option>
                                    <option value="Marketing Team" <?php echo (($_POST['resource_type'] ?? '') === 'Marketing Team') ? 'selected' : ''; ?>>Marketing Team</option>
                                </select>
                                <?php if (isset($contactErrors['resource_type'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($contactErrors['resource_type']); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="resource_count" class="form-label">How Many Resources You Need? *</label>
                                <select class="form-select <?php echo isset($contactErrors['resource_count']) ? 'is-invalid' : ''; ?>"
                                        id="resource_count" name="resource_count" required>
                                    <option value="">Select Count</option>
                                    <option value="1-5 Resources" <?php echo (($_POST['resource_count'] ?? '') === '1-5 Resources') ? 'selected' : ''; ?>>1-5 Resources</option>
                                    <option value="5-10 Resources" <?php echo (($_POST['resource_count'] ?? '') === '5-10 Resources') ? 'selected' : ''; ?>>5-10 Resources</option>
                                    <option value="10-20 Resources" <?php echo (($_POST['resource_count'] ?? '') === '10-20 Resources') ? 'selected' : ''; ?>>10-20 Resources</option>
                                    <option value="20-50 Resources" <?php echo (($_POST['resource_count'] ?? '') === '20-50 Resources') ? 'selected' : ''; ?>>20-50 Resources</option>
                                    <option value="50+ Resources" <?php echo (($_POST['resource_count'] ?? '') === '50+ Resources') ? 'selected' : ''; ?>>50+ Resources</option>
                                </select>
                                <?php if (isset($contactErrors['resource_count'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($contactErrors['resource_count']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-contact-submit w-100">Submit Enquiry</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Jobs -->
<section class="section" style="background-color: var(--cream);">
    <div class="container">
        <h2 class="section-title">Featured Jobs</h2>
        <p class="section-title">Latest opportunities from verified employers</p>
        
        <?php if ($featuredJobs): ?>
            <div class="row">
                <?php foreach ($featuredJobs as $job): ?>
                    <div class="col-lg-6 mb-4">
                        <div class="card job-card h-100">
                            <div class="card-body">
                                <div class="job-card-header">
                                    <div>
                                        <h4 class="job-title"><?php echo htmlspecialchars($job['title']); ?></h4>
                                        <p class="company-name">
                                            <?php if ($job['logo_path']): ?>
                                                <img src="<?php echo getFileUrl($job['logo_path'], 'company-logos'); ?>" 
                                                     alt="<?php echo htmlspecialchars($job['company_name']); ?>" 
                                                     class="company-logo-small me-2" style="width: 30px; height: 30px; border-radius: 4px;">
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($job['company_name']); ?>
                                        </p>
                                    </div>
                                    <span class="badge bg-success">Verified</span>
                                </div>
                                
                                <div class="job-meta">
                                    <div class="job-meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($job['city']); ?>, <?php echo htmlspecialchars($job['state']); ?>
                                    </div>
                                    <div class="job-meta-item">
                                        <i class="fas fa-clock"></i>
                                        <?php echo getEmploymentTypeLabel($job['employment_type']); ?>
                                    </div>
                                    <div class="job-meta-item">
                                        <i class="fas fa-laptop"></i>
                                        <?php echo getWorkModeLabel($job['work_mode']); ?>
                                    </div>
                                </div>
                                
                                <div class="job-meta">
                                    <div class="job-meta-item">
                                        <i class="fas fa-rupee-sign"></i>
                                        <?php echo formatSalaryRange($job['min_salary'], $job['max_salary']); ?>
                                    </div>
                                    <div class="job-meta-item">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo formatDate($job['posted_at']); ?>
                                    </div>
                                </div>
                                
                                <div class="job-tags">
                                    <span class="job-tag"><?php echo htmlspecialchars($job['category_name']); ?></span>
                                    <?php if ($job['min_experience'] > 0): ?>
                                        <span class="job-tag"><?php echo $job['min_experience']; ?>+ Years Exp</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="job-actions">
                                    <a href="<?php echo SITE_URL; ?>/job-details.php?id=<?php echo $job['id']; ?>" class="btn btn-primary btn-sm">View Details</a>
                                    <a href="<?php echo SITE_URL; ?>/jobs.php" class="btn btn-outline-primary btn-sm">More Jobs</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h3 class="empty-state-title">No Jobs Available</h3>
                <p class="empty-state-text">Check back later for new job opportunities</p>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <a href="<?php echo SITE_URL; ?>/jobs.php" class="btn btn-primary">View All Jobs</a>
        </div>
    </div>
</section>

<!-- Our Partners -->
<section class="section partners-section">
    <div class="container">
        <h2 class="section-title">Our Partners</h2>
        <p class="section-subtitle">Trusted by leading companies worldwide</p>

        <div class="partners-slider-wrapper">
            <div class="partners-slider">
                <!-- First set of logos -->
                <a href="https://www.google.com" target="_blank" class="partner-logo" title="Google">
                    <img src="https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png" alt="Google">
                </a>
                <a href="https://www.microsoft.com" target="_blank" class="partner-logo" title="Microsoft">
                    <img src="https://img.icons8.com/color/480/microsoft.png" alt="Microsoft">
                </a>
                <a href="https://www.amazon.com" target="_blank" class="partner-logo" title="Amazon">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a9/Amazon_logo.svg/200px-Amazon_logo.svg.png" alt="Amazon">
                </a>
                <a href="https://www.apple.com" target="_blank" class="partner-logo" title="Apple">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Apple_logo_black.svg/200px-Apple_logo_black.svg.png" alt="Apple">
                </a>
                <a href="https://www.meta.com" target="_blank" class="partner-logo" title="Meta">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Facebook_Logo_%282019%29.png/200px-Facebook_Logo_%282019%29.png" alt="Meta">
                </a>
                <a href="https://www.ibm.com" target="_blank" class="partner-logo" title="IBM">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/51/IBM_logo.svg/200px-IBM_logo.svg.png" alt="IBM">
                </a>
                <a href="https://www.oracle.com" target="_blank" class="partner-logo" title="Oracle">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a0/Oracle_logo.svg/200px-Oracle_logo.svg.png" alt="Oracle">
                </a>
                <a href="https://www.salesforce.com" target="_blank" class="partner-logo" title="Salesforce">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f9/Salesforce_logo.svg/200px-Salesforce_logo.svg.png" alt="Salesforce">
                </a>
                <!-- Duplicate set for infinite scroll -->
                <a href="https://www.google.com" target="_blank" class="partner-logo" title="Google">
                    <img src="https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png" alt="Google">
                </a>
                <a href="https://www.microsoft.com" target="_blank" class="partner-logo" title="Microsoft">
                    <img src="https://img.icons8.com/color/480/microsoft.png" alt="Microsoft">
                </a>
                <a href="https://www.amazon.com" target="_blank" class="partner-logo" title="Amazon">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a9/Amazon_logo.svg/200px-Amazon_logo.svg.png" alt="Amazon">
                </a>
                <a href="https://www.apple.com" target="_blank" class="partner-logo" title="Apple">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Apple_logo_black.svg/200px-Apple_logo_black.svg.png" alt="Apple">
                </a>
                <a href="https://www.meta.com" target="_blank" class="partner-logo" title="Meta">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Facebook_Logo_%282019%29.png/200px-Facebook_Logo_%282019%29.png" alt="Meta">
                </a>
                <a href="https://www.ibm.com" target="_blank" class="partner-logo" title="IBM">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/51/IBM_logo.svg/200px-IBM_logo.svg.png" alt="IBM">
                </a>
                <a href="https://www.oracle.com" target="_blank" class="partner-logo" title="Oracle">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a0/Oracle_logo.svg/200px-Oracle_logo.svg.png" alt="Oracle">
                </a>
                <a href="https://www.salesforce.com" target="_blank" class="partner-logo" title="Salesforce">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f9/Salesforce_logo.svg/200px-Salesforce_logo.svg.png" alt="Salesforce">
                </a>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="section" style="background-color: var(--cream);">
    <div class="container">
        <h2 class="section-title">How FindNaukri Works</h2>
        <p class="section-subtitle">Simple steps to land your dream job</p>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-user-plus fa-3x" style="color: var(--primary-orange);"></i>
                        </div>
                        <h4>Create Profile</h4>
                        <p class="card-text">Register and build your professional profile with your skills, experience, and resume.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-search fa-3x" style="color: var(--primary-orange);"></i>
                        </div>
                        <h4>Discover Jobs</h4>
                        <p class="card-text">Search and filter through thousands of job opportunities from verified employers.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-handshake fa-3x" style="color: var(--primary-orange);"></i>
                        </div>
                        <h4>Get Hired</h4>
                        <p class="card-text">Apply to jobs, track applications, and get hired by top companies.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="section" style="background: linear-gradient(135deg, var(--cream) 0%, var(--light-gray) 100%); color: var(--dark-charcoal);">
    <div class="container text-center">
        <h2 class="mb-3">Ready to Start Your Job Search?</h2>
        <p class="mb-4" style="color: var(--text-gray);">Join thousands of job seekers who found their dream jobs on FindNaukri</p>
        <div>
            <a href="<?php echo SITE_URL; ?>/candidate-register.php" class="btn btn-primary btn-lg me-3">Create Account</a>
            <a href="<?php echo SITE_URL; ?>/jobs.php" class="btn btn-outline-primary btn-lg">Browse Jobs</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
