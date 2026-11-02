<?php
/**
 * FindNaukri.in - Job Details Page
 */

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/upload.php';
require_once 'includes/csrf.php';

$jobId = (int)($_GET['id'] ?? 0);

if (!$jobId) {
    redirect(SITE_URL . '/jobs.php');
}

// Get job details
$query = "SELECT j.*, e.company_name, e.logo_path, e.description as company_description, e.verification_status, 
          e.website, e.city as company_city, e.state as company_state, c.name as category_name 
          FROM jobs j
          JOIN employers e ON j.employer_id = e.id
          JOIN job_categories c ON j.category_id = c.id
          WHERE j.id = ? LIMIT 1";
$job = fetchOne($query, [$jobId]);

if (!$job) {
    setFlash('error', 'Job not found');
    redirect(SITE_URL . '/jobs.php');
}

// Check if job is approved and active
if ($job['approval_status'] !== 'approved' || $job['job_status'] !== 'active') {
    setFlash('error', 'This job is not currently available');
    redirect(SITE_URL . '/jobs.php');
}

// Check if job deadline has passed
if ($job['deadline'] && $job['deadline'] < date('Y-m-d')) {
    setFlash('error', 'This job listing has expired');
    redirect(SITE_URL . '/jobs.php');
}

$pageTitle = $job['title'] . ' - ' . $job['company_name'];

// Check if user has already applied
$hasApplied = false;
$isSaved = false;
if (isLoggedIn() && isCandidate()) {
    $userId = getCurrentUserId();
    $hasApplied = hasApplied($userId, $jobId);
    $isSaved = isJobSaved($userId, $jobId);
}

// Get related jobs
$query = "SELECT j.*, e.company_name 
          FROM jobs j
          JOIN employers e ON j.employer_id = e.id
          WHERE j.category_id = ? AND j.id != ? AND j.approval_status = 'approved' AND j.job_status = 'active'
          ORDER BY j.posted_at DESC
          LIMIT 4";
$relatedJobs = fetchAll($query, [$job['category_id'], $jobId]);

require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mt-3">
    <div class="container">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/jobs.php">Jobs</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($job['title']); ?></li>
        </ol>
    </div>
</nav>

<!-- Job Details -->
<section class="section">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h1 class="h3 mb-2"><?php echo htmlspecialchars($job['title']); ?></h1>
                                <p class="text-muted mb-0">
                                    <?php if ($job['logo_path']): ?>
                                        <img src="<?php echo getFileUrl($job['logo_path'], 'company-logos'); ?>" 
                                             alt="<?php echo htmlspecialchars($job['company_name']); ?>" 
                                             class="me-2" style="width: 40px; height: 40px; border-radius: 8px;">
                                    <?php endif; ?>
                                    <strong><?php echo htmlspecialchars($job['company_name']); ?></strong>
                                    <?php if ($job['verification_status'] === 'verified'): ?>
                                        <span class="badge bg-success ms-2"><i class="fas fa-check-circle"></i> Verified</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div>
                                <?php if (isLoggedIn() && isCandidate()): ?>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="shareJob()">
                                        <i class="fas fa-share-alt"></i> Share
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="job-meta mb-4">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    <?php echo htmlspecialchars($job['city']); ?>, <?php echo htmlspecialchars($job['state']); ?>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <i class="fas fa-clock text-primary me-2"></i>
                                    <?php echo getEmploymentTypeLabel($job['employment_type']); ?>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <i class="fas fa-laptop text-primary me-2"></i>
                                    <?php echo getWorkModeLabel($job['work_mode']); ?>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <i class="fas fa-calendar text-primary me-2"></i>
                                    Posted: <?php echo formatDate($job['posted_at']); ?>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <i class="fas fa-calendar-times text-primary me-2"></i>
                                    Deadline: <?php echo formatDate($job['deadline']); ?>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <i class="fas fa-users text-primary me-2"></i>
                                    <?php echo $job['openings']; ?> Opening(s)
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5 class="mb-3">Salary</h5>
                            <p class="h4 text-primary"><?php echo formatSalaryRange($job['min_salary'], $job['max_salary']); ?></p>
                            <small class="text-muted">Per annum</small>
                        </div>
                        
                        <div class="mb-4">
                            <h5 class="mb-3">Job Description</h5>
                            <div class="text-muted" style="line-height: 1.8;">
                                <?php echo nl2br(htmlspecialchars($job['description'])); ?>
                            </div>
                        </div>
                        
                        <?php if ($job['responsibilities']): ?>
                        <div class="mb-4">
                            <h5 class="mb-3">Responsibilities</h5>
                            <ul class="text-muted">
                                <?php foreach (explode("\n", $job['responsibilities']) as $responsibility): ?>
                                    <?php if (trim($responsibility)): ?>
                                        <li><?php echo htmlspecialchars(trim($responsibility)); ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($job['required_skills']): ?>
                        <div class="mb-4">
                            <h5 class="mb-3">Required Skills</h5>
                            <div class="job-tags">
                                <?php foreach (explode(',', $job['required_skills']) as $skill): ?>
                                    <?php if (trim($skill)): ?>
                                        <span class="job-tag"><?php echo htmlspecialchars(trim($skill)); ?></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-4">
                            <h5 class="mb-3">Requirements</h5>
                            <ul class="text-muted">
                                <li>Experience: <?php echo $job['min_experience'] ? $job['min_experience'] . '+ years' : 'Fresher welcome'; ?>
                                    <?php if ($job['max_experience']): ?> to <?php echo $job['max_experience']; ?> years<?php endif; ?>
                                </li>
                                <?php if ($job['qualification']): ?>
                                    <li>Qualification: <?php echo htmlspecialchars($job['qualification']); ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        
                        <div class="d-flex gap-2 mt-4">
                            <?php if (isLoggedIn() && isCandidate()): ?>
                                <?php if ($hasApplied): ?>
                                    <button class="btn btn-success" disabled>
                                        <i class="fas fa-check-circle me-2"></i>Already Applied
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-primary btn-lg apply-job-btn" data-job-id="<?php echo $job['id']; ?>">
                                        <i class="fas fa-paper-plane me-2"></i>Apply Now
                                    </button>
                                <?php endif; ?>

                                <?php if ($isSaved): ?>
                                    <button class="btn btn-outline-secondary save-job-btn saved" data-job-id="<?php echo $job['id']; ?>">
                                        <i class="fas fa-bookmark"></i> Saved
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-outline-secondary save-job-btn" data-job-id="<?php echo $job['id']; ?>">
                                        <i class="far fa-bookmark"></i> Save Job
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="<?php echo SITE_URL; ?>/candidate-login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>"
                                   class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Apply Now
                                </a>
                                <a href="<?php echo SITE_URL; ?>/candidate-login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>"
                                   class="btn btn-outline-secondary">
                                    <i class="far fa-bookmark"></i> Save Job
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Company Info -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">About Company</h5>
                        <div class="d-flex align-items-start mb-3">
                            <?php if ($job['logo_path']): ?>
                                <img src="<?php echo getFileUrl($job['logo_path'], 'company-logos'); ?>" 
                                     alt="<?php echo htmlspecialchars($job['company_name']); ?>" 
                                     class="me-3" style="width: 60px; height: 60px; border-radius: 8px;">
                            <?php endif; ?>
                            <div>
                                <h6 class="mb-1"><?php echo htmlspecialchars($job['company_name']); ?></h6>
                                <p class="text-muted small mb-1">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <?php echo htmlspecialchars($job['company_city'] ?? ''); ?>, <?php echo htmlspecialchars($job['company_state'] ?? ''); ?>
                                </p>
                                <?php if ($job['website']): ?>
                                    <a href="<?php echo htmlspecialchars($job['website']); ?>" target="_blank" class="small">
                                        <i class="fas fa-globe me-1"></i> Visit Website
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($job['company_description']): ?>
                            <p class="text-muted"><?php echo htmlspecialchars($job['company_description']); ?></p>
                        <?php endif; ?>
                        <a href="<?php echo SITE_URL; ?>/companies.php" class="btn btn-outline-primary btn-sm">View All Jobs</a>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Job Overview</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted">Experience</td>
                                <td class="text-end"><?php echo $job['min_experience'] ? $job['min_experience'] . '+ years' : 'Fresher'; ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Industry</td>
                                <td class="text-end"><?php echo htmlspecialchars($job['category_name']); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Employment Type</td>
                                <td class="text-end"><?php echo getEmploymentTypeLabel($job['employment_type']); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Work Mode</td>
                                <td class="text-end"><?php echo getWorkModeLabel($job['work_mode']); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Role</td>
                                <td class="text-end"><?php echo htmlspecialchars($job['title']); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Share this job</h5>
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-outline-primary btn-sm"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="btn btn-outline-info btn-sm"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="btn btn-outline-primary btn-sm"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="btn btn-outline-success btn-sm"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
                
                <?php if ($relatedJobs): ?>
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">Similar Jobs</h5>
                        <?php foreach ($relatedJobs as $relatedJob): ?>
                            <div class="mb-3 pb-3 border-bottom">
                                <h6 class="mb-1">
                                    <a href="<?php echo SITE_URL; ?>/job-details.php?id=<?php echo $relatedJob['id']; ?>" 
                                       class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($relatedJob['title']); ?>
                                    </a>
                                </h6>
                                <p class="text-muted small mb-1"><?php echo htmlspecialchars($relatedJob['company_name']); ?></p>
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <?php echo htmlspecialchars($relatedJob['city']); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
function shareJob() {
    const jobTitle = '<?php echo htmlspecialchars($job['title']); ?>';
    const jobUrl = window.location.href;
    const shareText = 'Check out this job opportunity at <?php echo htmlspecialchars($job['company_name']); ?>: ' + jobTitle;

    if (navigator.share) {
        navigator.share({
            title: jobTitle,
            text: shareText,
            url: jobUrl
        }).catch(err => {
            console.log('Share failed:', err);
            fallbackShare(jobUrl);
        });
    } else {
        fallbackShare(jobUrl);
    }
}

function fallbackShare(url) {
    // Try to copy to clipboard
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(() => {
            alert('Job link copied to clipboard!');
        }).catch(err => {
            console.log('Clipboard failed:', err);
            manualShare(url);
        });
    } else {
        manualShare(url);
    }
}

function manualShare(url) {
    // Create a temporary input to copy text
    const tempInput = document.createElement('input');
    tempInput.value = url;
    document.body.appendChild(tempInput);
    tempInput.select();
    try {
        document.execCommand('copy');
        alert('Job link copied to clipboard!');
    } catch (err) {
        // If copy fails, show the link in a prompt
        prompt('Copy this job link:', url);
    }
    document.body.removeChild(tempInput);
}

// Apply for job
document.querySelectorAll('.apply-job-btn').forEach(button => {
    button.addEventListener('click', function() {
        const jobId = this.dataset.jobId;

        if (confirm('Are you sure you want to apply for this job?')) {
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying...';

            fetch('<?php echo SITE_URL; ?>/api/apply-job.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    job_id: jobId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Application submitted successfully!');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to apply. Please try again.');
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Apply Now';

                    if (data.require_resume) {
                        if (confirm('You need to upload your resume first. Go to profile page?')) {
                            window.location.href = '<?php echo SITE_URL; ?>/candidate/edit-profile.php';
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Apply Now';
            });
        }
    });
});

// Save/Unsave job
document.querySelectorAll('.save-job-btn').forEach(button => {
    button.addEventListener('click', function() {
        const jobId = this.dataset.jobId;
        const isSaved = this.classList.contains('saved');
        const action = isSaved ? 'remove' : 'add';

        this.disabled = true;

        fetch('<?php echo SITE_URL; ?>/api/toggle-save-job.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                job_id: jobId,
                action: action
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (isSaved) {
                    this.classList.remove('saved');
                    this.innerHTML = '<i class="far fa-bookmark"></i> Save Job';
                } else {
                    this.classList.add('saved');
                    this.innerHTML = '<i class="fas fa-bookmark"></i> Saved';
                }
            } else {
                alert(data.message || 'Failed to update saved status');
            }
            this.disabled = false;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            this.disabled = false;
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
