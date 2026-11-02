<?php
/**
 * FindNaukri.in - About Us Page
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

$pageTitle = 'About Us - FindNaukri.in';

require_once 'includes/header.php';
?>

<!-- Page Header -->
<section class="section" style="background-color: var(--light-gray); color: var(--dark-charcoal); padding: 3rem 0;">
    <div class="container text-center">
        <h1 class="mb-2">About FindNaukri.in</h1>
        <p class="mb-0" style="color: var(--text-gray);">Connecting Talent with Opportunities</p>
    </div>
</section>

<!-- Mission Section -->
<section class="section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4">
                <h2 class="section-title text-start">Our Mission</h2>
                <p class="text-muted mb-3">
                    At FindNaukri.in, we believe that every deserving candidate deserves the right opportunity, and every employer deserves access to the best talent. Our mission is to bridge this gap through technology, transparency, and trust.
                </p>
                <p class="text-muted mb-3">
                    We are committed to simplifying the recruitment process for both job seekers and employers by providing a platform that is easy to use, reliable, and effective.
                </p>
                <p class="text-muted">
                    Since our inception, we have helped thousands of professionals find their dream jobs and assisted hundreds of companies in building their teams.
                </p>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-handshake fa-5x text-primary mb-4"></i>
                        <h4>Trusted by</h4>
                        <div class="row mt-4">
                            <div class="col-4">
                                <h3 class="text-primary mb-0">5000+</h3>
                                <small class="text-muted">Job Seekers</small>
                            </div>
                            <div class="col-4">
                                <h3 class="text-primary mb-0">500+</h3>
                                <small class="text-muted">Employers</small>
                            </div>
                            <div class="col-4">
                                <h3 class="text-primary mb-0">2000+</h3>
                                <small class="text-muted">Placements</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="section" style="background-color: var(--cream);">
    <div class="container">
        <h2 class="section-title text-center mb-5">Our Values</h2>
        
        <div class="row">
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-shield-alt fa-3x text-primary"></i>
                        </div>
                        <h5>Trust</h5>
                        <p class="text-muted small">We verify employers and job postings to ensure a trustworthy platform for all users.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-eye fa-3x text-primary"></i>
                        </div>
                        <h5>Transparency</h5>
                        <p class="text-muted small">We maintain clear communication and honest practices throughout the recruitment process.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-lightbulb fa-3x text-primary"></i>
                        </div>
                        <h5>Opportunity</h5>
                        <p class="text-muted small">We strive to create equal opportunities for all candidates regardless of background.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-chart-line fa-3x text-primary"></i>
                        </div>
                        <h5>Growth</h5>
                        <p class="text-muted small">We support the professional growth of both candidates and the organizations they join.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How We Work -->
<section class="section">
    <div class="container">
        <h2 class="section-title text-center mb-5">How We Work</h2>
        
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge bg-primary fs-4">1</span>
                        </div>
                        <h5>For Candidates</h5>
                        <ul class="text-muted">
                            <li>Create a detailed professional profile</li>
                            <li>Upload your resume and showcase skills</li>
                            <li>Search and apply to relevant jobs</li>
                            <li>Track applications and get updates</li>
                            <li>Connect with verified employers</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge bg-primary fs-4">2</span>
                        </div>
                        <h5>For Employers</h5>
                        <ul class="text-muted">
                            <li>Register and verify your company</li>
                            <li>Post detailed job descriptions</li>
                            <li>Review candidate profiles and resumes</li>
                            <li>Shortlist and interview candidates</li>
                            <li>Make hiring decisions efficiently</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge bg-primary fs-4">3</span>
                        </div>
                        <h5>Our Role</h5>
                        <ul class="text-muted">
                            <li>Verify all employer registrations</li>
                            <li>Review and approve job postings</li>
                            <li>Ensure platform security and privacy</li>
                            <li>Provide support to all users</li>
                            <li>Continuously improve the platform</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="section" style="background-color: var(--cream);">
    <div class="container">
        <h2 class="section-title text-center mb-5">Our Team</h2>
        
        <div class="row justify-content-center">
            <div class="col-md-4 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-user-tie fa-4x text-muted"></i>
                        </div>
                        <h5>Leadership Team</h5>
                        <p class="text-muted small">Experienced professionals dedicated to transforming recruitment in India.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-users fa-4x text-muted"></i>
                        </div>
                        <h5>Support Team</h5>
                        <p class="text-muted small">Dedicated support staff available to assist candidates and employers.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-code fa-4x text-muted"></i>
                        </div>
                        <h5>Tech Team</h5>
                        <p class="text-muted small">Skilled developers maintaining and improving our platform.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section" style="background: linear-gradient(135deg, var(--cream) 0%, var(--light-gray) 100%); color: var(--dark-charcoal);">
    <div class="container text-center">
        <h2 class="mb-3">Join the FindNaukri Community</h2>
        <p class="mb-4" style="color: var(--text-gray);">Whether you're looking for a job or looking to hire, we're here to help.</p>
        <div>
            <a href="<?php echo SITE_URL; ?>/candidate-register.php" class="btn btn-primary btn-lg me-3">Find a Job</a>
            <a href="<?php echo SITE_URL; ?>/employer-register.php" class="btn btn-outline-primary btn-lg">Hire Talent</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
