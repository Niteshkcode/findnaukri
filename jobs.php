<?php
/**
 * FindNaukri.in - Job Search Page
 */

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'includes/upload.php';

$pageTitle = 'Find Jobs - FindNaukri.in';

// Get search parameters
$keyword = sanitize($_GET['keyword'] ?? '');
$location = sanitize($_GET['location'] ?? '');
$category = (int)($_GET['category'] ?? 0);
$experience = sanitize($_GET['experience'] ?? '');
$salaryMin = (int)($_GET['salary_min'] ?? 0);
$salaryMax = (int)($_GET['salary_max'] ?? 0);
$employmentType = sanitize($_GET['employment_type'] ?? '');
$workMode = sanitize($_GET['work_mode'] ?? '');
$sortBy = sanitize($_GET['sort'] ?? 'newest');

// Pagination
$page = (int)($_GET['page'] ?? 1);
$offset = ($page - 1) * JOBS_PER_PAGE;

// Build query
$where = ["j.approval_status = 'approved'", "j.job_status = 'active'", "(j.deadline IS NULL OR j.deadline >= CURDATE())"];
$params = [];

if ($keyword) {
    $where[] = "(j.title LIKE ? OR j.description LIKE ? OR j.required_skills LIKE ?)";
    $keywordParam = "%$keyword%";
    $params[] = $keywordParam;
    $params[] = $keywordParam;
    $params[] = $keywordParam;
}

if ($location) {
    $where[] = "(j.city LIKE ? OR j.state LIKE ?)";
    $locationParam = "%$location%";
    $params[] = $locationParam;
    $params[] = $locationParam;
}

if ($category) {
    $where[] = "j.category_id = ?";
    $params[] = $category;
}

if ($experience) {
    if ($experience === 'fresher') {
        $where[] = "j.min_experience = 0";
    } else {
        $expParts = explode('-', $experience);
        if (count($expParts) === 2) {
            $where[] = "j.min_experience <= ?";
            $params[] = (int)$expParts[1];
        }
    }
}

if ($salaryMin) {
    $where[] = "(j.min_salary >= ? OR j.min_salary IS NULL)";
    $params[] = $salaryMin;
}

if ($salaryMax) {
    $where[] = "(j.max_salary <= ? OR j.max_salary IS NULL)";
    $params[] = $salaryMax;
}

if ($employmentType) {
    $where[] = "j.employment_type = ?";
    $params[] = $employmentType;
}

if ($workMode) {
    $where[] = "j.work_mode = ?";
    $params[] = $workMode;
}

$whereClause = implode(' AND ', $where);

// Order by
$orderClause = match($sortBy) {
    'salary_high' => 'j.max_salary DESC',
    'salary_low' => 'j.min_salary ASC',
    'relevance' => 'j.posted_at DESC',
    default => 'j.posted_at DESC'
};

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM jobs j WHERE $whereClause";
$totalResult = fetchOne($countQuery, $params);
$totalJobs = $totalResult['total'] ?? 0;

// Get jobs
$query = "SELECT j.*, e.company_name, e.logo_path, e.verification_status, c.name as category_name 
          FROM jobs j
          JOIN employers e ON j.employer_id = e.id
          JOIN job_categories c ON j.category_id = c.id
          WHERE $whereClause
          ORDER BY $orderClause
          LIMIT ? OFFSET ?";
$params[] = JOBS_PER_PAGE;
$params[] = $offset;

$jobs = fetchAll($query, $params);

// Get categories for filter
$categories = fetchAll("SELECT * FROM job_categories WHERE status = 'active' ORDER BY name");

require_once 'includes/header.php';
?>

<!-- Page Header -->
<section class="section" style="background-color: var(--light-gray); color: var(--dark-charcoal); padding: 2rem 0;">
    <div class="container">
        <h1 class="mb-2">Find Jobs</h1>
        <p class="mb-0" style="color: var(--text-gray);">Discover your next career opportunity</p>
    </div>
</section>

<!-- Search and Filters -->
<section class="section" style="background-color: var(--cream);">
    <div class="container">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="keyword" placeholder="Job title, skills, or company" value="<?php echo htmlspecialchars($keyword); ?>">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="location" placeholder="City or location" value="<?php echo htmlspecialchars($location); ?>">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="category">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $category === $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="employment_type">
                                <option value="">All Types</option>
                                <option value="full-time" <?php echo $employmentType === 'full-time' ? 'selected' : ''; ?>>Full Time</option>
                                <option value="part-time" <?php echo $employmentType === 'part-time' ? 'selected' : ''; ?>>Part Time</option>
                                <option value="contract" <?php echo $employmentType === 'contract' ? 'selected' : ''; ?>>Contract</option>
                                <option value="internship" <?php echo $employmentType === 'internship' ? 'selected' : ''; ?>>Internship</option>
                                <option value="freelance" <?php echo $employmentType === 'freelance' ? 'selected' : ''; ?>>Freelance</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-3">
                            <select class="form-select" name="experience">
                                <option value="">Experience</option>
                                <option value="fresher" <?php echo $experience === 'fresher' ? 'selected' : ''; ?>>Fresher</option>
                                <option value="0-1" <?php echo $experience === '0-1' ? 'selected' : ''; ?>>0-1 Years</option>
                                <option value="1-3" <?php echo $experience === '1-3' ? 'selected' : ''; ?>>1-3 Years</option>
                                <option value="3-5" <?php echo $experience === '3-5' ? 'selected' : ''; ?>>3-5 Years</option>
                                <option value="5-10" <?php echo $experience === '5-10' ? 'selected' : ''; ?>>5-10 Years</option>
                                <option value="10+" <?php echo $experience === '10+' ? 'selected' : ''; ?>>10+ Years</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="work_mode">
                                <option value="">Work Mode</option>
                                <option value="on-site" <?php echo $workMode === 'on-site' ? 'selected' : ''; ?>>On-site</option>
                                <option value="remote" <?php echo $workMode === 'remote' ? 'selected' : ''; ?>>Remote</option>
                                <option value="hybrid" <?php echo $workMode === 'hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" name="salary_min" placeholder="Min Salary" value="<?php echo $salaryMin; ?>">
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" name="salary_max" placeholder="Max Salary" value="<?php echo $salaryMax; ?>">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="sort">
                                <option value="newest" <?php echo $sortBy === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                                <option value="salary_high" <?php echo $sortBy === 'salary_high' ? 'selected' : ''; ?>>Highest Salary</option>
                                <option value="salary_low" <?php echo $sortBy === 'salary_low' ? 'selected' : ''; ?>>Lowest Salary</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="<?php echo SITE_URL; ?>/jobs.php" class="btn btn-sm btn-outline-secondary">Clear Filters</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Results -->
<section class="section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5><?php echo $totalJobs; ?> Jobs Found</h5>
        </div>
        
        <?php if ($jobs): ?>
            <div class="row">
                <?php foreach ($jobs as $job): ?>
                    <div class="col-lg-6 mb-4">
                        <div class="card job-card h-100">
                            <div class="card-body">
                                <div class="job-card-header">
                                    <div>
                                        <h4 class="job-title">
                                            <a href="<?php echo SITE_URL; ?>/job-details.php?id=<?php echo $job['id']; ?>" style="color: inherit; text-decoration: none;">
                                                <?php echo htmlspecialchars($job['title']); ?>
                                            </a>
                                        </h4>
                                        <p class="company-name">
                                            <?php if ($job['logo_path']): ?>
                                                <img src="<?php echo getFileUrl($job['logo_path'], 'company-logos'); ?>" 
                                                     alt="<?php echo htmlspecialchars($job['company_name']); ?>" 
                                                     class="company-logo-small me-2" style="width: 30px; height: 30px; border-radius: 4px;">
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($job['company_name']); ?>
                                            <?php if ($job['verification_status'] === 'verified'): ?>
                                                <i class="fas fa-check-circle text-success ms-1"></i>
                                            <?php endif; ?>
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
                                    <?php else: ?>
                                        <span class="job-tag">Fresher</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="job-actions">
                                    <a href="<?php echo SITE_URL; ?>/job-details.php?id=<?php echo $job['id']; ?>" class="btn btn-primary btn-sm">View Details</a>
                                    <?php if (isLoggedIn() && isCandidate()): ?>
                                        <?php if (isJobSaved(getCurrentUserId(), $job['id'])): ?>
                                            <button class="btn btn-outline-secondary btn-sm saved" data-job-id="<?php echo $job['id']; ?>">
                                                <i class="fas fa-bookmark"></i> Saved
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-outline-secondary btn-sm save-job-btn" data-job-id="<?php echo $job['id']; ?>">
                                                <i class="far fa-bookmark"></i> Save Job
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php echo buildPagination($totalJobs, JOBS_PER_PAGE, $page, SITE_URL . '/jobs.php?' . http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY))); ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="empty-state-title">No Jobs Found</h3>
                <p class="empty-state-text">Try adjusting your search filters or check back later for new opportunities</p>
                <a href="<?php echo SITE_URL; ?>/jobs.php" class="btn btn-primary">Clear Filters</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
