<?php
/**
 * FindNaukri.in - Services Page
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

$pageTitle = 'Our Services - FindNaukri.in';

require_once 'includes/header.php';
?>

<!-- Page Header -->
<section class="section" style="background-color: var(--light-gray); color: var(--dark-charcoal); padding: 2rem 0;">
    <div class="container">
        <h1 class="mb-2">Our Services</h1>
        <p class="mb-0" style="color: var(--text-gray);">Comprehensive recruitment solutions for employers and candidates</p>
    </div>
</section>

<!-- Services Overview -->
<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3">
                                <i class="fas fa-search fa-3x text-primary"></i>
                            </div>
                            <div>
                                <h5>Job Search & Matching</h5>
                                <p class="text-muted mb-0">Advanced job search with intelligent matching based on skills, experience, and preferences.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3">
                                <i class="fas fa-building fa-3x text-primary"></i>
                            </div>
                            <div>
                                <h5>Employer Branding</h5>
                                <p class="text-muted mb-0">Showcase your company culture and attract top talent with enhanced company profiles.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3">
                                <i class="fas fa-users fa-3x text-primary"></i>
                            </div>
                            <div>
                                <h5>Candidate Sourcing</h5>
                                <p class="text-muted mb-0">Access a large database of qualified candidates and filter based on your requirements.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3">
                                <i class="fas fa-file-alt fa-3x text-primary"></i>
                            </div>
                            <div>
                                <h5>Resume Database</h5>
                                <p class="text-muted mb-0">Search and access candidate resumes with permission-based viewing.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3">
                                <i class="fas fa-graduation-cap fa-3x text-primary"></i>
                            </div>
                            <div>
                                <h5>Fresher Recruitment</h5>
                                <p class="text-muted mb-0">Specialized programs to connect fresh graduates with entry-level opportunities.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3">
                                <i class="fas fa-headset fa-3x text-primary"></i>
                            </div>
                            <div>
                                <h5>Dedicated Support</h5>
                                <p class="text-muted mb-0">Personalized support for both candidates and employers throughout the process.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- For Employers -->
<section class="section" style="background-color: var(--cream);">
    <div class="container">
        <h2 class="section-title text-center mb-5">For Employers</h2>
        
        <div class="row">
            <div class="col-lg-6 mb-4">
                <h5 class="mb-3">How We Help You Hire</h5>
                <ul class="text-muted">
                    <li class="mb-2">Post unlimited job listings</li>
                    <li class="mb-2">Access a large candidate database</li>
                    <li class="mb-2">Screen and shortlist candidates efficiently</li>
                    <li class="mb-2">Schedule and manage interviews</li>
                    <li class="mb-2">Track recruitment metrics</li>
                    <li class="mb-2">Get dedicated account management</li>
                </ul>
            </div>
            
            <div class="col-lg-6 mb-4">
                <h5 class="mb-3">Recruitment Process</h5>
                <ol class="text-muted">
                    <li class="mb-2">Register and verify your company</li>
                    <li class="mb-2">Post detailed job descriptions</li>
                    <li class="mb-2">Review applications as they come in</li>
                    <li class="mb-2">Shortlist promising candidates</li>
                    <li class="mb-2">Conduct interviews</li>
                    <li class="mb-2">Make offers and hire</li>
                </ol>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?php echo SITE_URL; ?>/employer-register.php" class="btn btn-primary btn-lg">Start Hiring Today</a>
        </div>
    </div>
</section>

<!-- For Candidates -->
<section class="section">
    <div class="container">
        <h2 class="section-title text-center mb-5">For Candidates</h2>
        
        <div class="row">
            <div class="col-lg-6 mb-4">
                <h5 class="mb-3">How We Help You Find Jobs</h5>
                <ul class="text-muted">
                    <li class="mb-2">Create a professional profile</li>
                    <li class="mb-2">Upload and manage your resume</li>
                    <li class="mb-2">Search jobs by various criteria</li>
                    <li class="mb-2">Apply to multiple jobs easily</li>
                    <li class="mb-2">Track application status</li>
                    <li class="mb-2">Get job alerts for matching positions</li>
                </ul>
            </div>
            
            <div class="col-lg-6 mb-4">
                <h5 class="mb-3">Job Search Process</h5>
                <ol class="text-muted">
                    <li class="mb-2">Register and complete your profile</li>
                    <li class="mb-2">Upload your resume</li>
                    <li class="mb-2">Search for relevant jobs</li>
                    <li class="mb-2">Apply to positions of interest</li>
                    <li class="mb-2">Attend interviews</li>
                    <li class="mb-2">Get hired and grow your career</li>
                </ol>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?php echo SITE_URL; ?>/candidate-register.php" class="btn btn-primary btn-lg">Start Your Job Search</a>
        </div>
    </div>
</section>

<!-- Contact CTA -->
<section class="section" style="background: linear-gradient(135deg, var(--cream) 0%, var(--light-gray) 100%); color: var(--dark-charcoal);">
    <div class="container text-center">
        <h2 class="mb-3">Need Custom Solutions?</h2>
        <p class="mb-4" style="color: var(--text-gray);">Contact us for tailored recruitment services for your organization</p>
        <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary btn-lg">Contact Us</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
