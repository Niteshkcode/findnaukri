<?php
/**
 * FindNaukri.in - Companies Directory
 */

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Companies - FindNaukri.in';

// Get search parameters
$search = sanitize($_GET['search'] ?? '');
$industry = sanitize($_GET['industry'] ?? '');
$location = sanitize($_GET['location'] ?? '');

// Pagination
$page = (int)($_GET['page'] ?? 1);
$offset = ($page - 1) * EMPLOYERS_PER_PAGE;

// Build query
$where = ["e.verification_status = 'verified'"];
$params = [];

if ($search) {
    $where[] = "(e.company_name LIKE ? OR e.description LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
}

if ($industry) {
    $where[] = "e.industry LIKE ?";
    $params[] = "%$industry%";
}

if ($location) {
    $where[] = "(e.city LIKE ? OR e.state LIKE ?)";
    $locationParam = "%$location%";
    $params[] = $locationParam;
    $params[] = $locationParam;
}

$whereClause = implode(' AND ', $where);

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM employers e WHERE $whereClause";
$totalResult = fetchOne($countQuery, $params);
$totalCompanies = $totalResult['total'] ?? 0;

// Get companies
$query = "SELECT e.*, COUNT(j.id) as job_count 
          FROM employers e
          LEFT JOIN jobs j ON e.id = j.employer_id AND j.approval_status = 'approved' AND j.job_status = 'active'
          WHERE $whereClause
          GROUP BY e.id
          ORDER BY job_count DESC, e.created_at DESC
          LIMIT ? OFFSET ?";
$params[] = EMPLOYERS_PER_PAGE;
$params[] = $offset;

$companies = fetchAll($query, $params);

// Get industries for filter
$industries = fetchAll("SELECT DISTINCT industry FROM employers WHERE verification_status = 'verified' AND industry IS NOT NULL ORDER BY industry");

require_once 'includes/header.php';
?>

<!-- Page Header -->
<section class="section" style="background-color: var(--light-gray); color: var(--dark-charcoal); padding: 2rem 0;">
    <div class="container">
        <h1 class="mb-2">Companies</h1>
        <p class="mb-0" style="color: var(--text-gray);">Discover top employers and their job opportunities</p>
    </div>
</section>

<!-- Search and Filters -->
<section class="section" style="background-color: var(--cream);">
    <div class="container">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="search" placeholder="Company name" value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="industry">
                                <option value="">All Industries</option>
                                <?php foreach ($industries as $ind): ?>
                                    <option value="<?php echo htmlspecialchars($ind['industry']); ?>" 
                                            <?php echo $industry === $ind['industry'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($ind['industry']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="location" placeholder="City or state" value="<?php echo htmlspecialchars($location); ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="<?php echo SITE_URL; ?>/companies.php" class="btn btn-sm btn-outline-secondary">Clear Filters</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Companies List -->
<section class="section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5><?php echo $totalCompanies; ?> Companies Found</h5>
        </div>
        
        <?php if ($companies): ?>
            <div class="row">
                <?php foreach ($companies as $company): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card company-card h-100">
                            <div class="card-body">
                                <?php if ($company['logo_path']): ?>
                                    <img src="<?php echo getFileUrl($company['logo_path'], 'company-logos'); ?>" 
                                         alt="<?php echo htmlspecialchars($company['company_name']); ?>" 
                                         class="company-logo">
                                <?php else: ?>
                                    <div class="company-logo">
                                        <i class="fas fa-building fa-2x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <h5 class="company-name-display"><?php echo htmlspecialchars($company['company_name']); ?></h5>
                                <p class="company-industry"><?php echo htmlspecialchars($company['industry'] ?? 'Various Industries'); ?></p>
                                <p class="company-location">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <?php echo htmlspecialchars($company['city'] ?? ''); ?>, <?php echo htmlspecialchars($company['state'] ?? ''); ?>
                                </p>
                                
                                <div class="verified-badge mb-2">
                                    <i class="fas fa-check-circle"></i> Verified Employer
                                </div>
                                
                                <p class="text-muted small mb-3">
                                    <i class="fas fa-briefcase me-1"></i>
                                    <?php echo $company['job_count']; ?> Active Job(s)
                                </p>
                                
                                <?php if ($company['description']): ?>
                                    <p class="text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?php echo htmlspecialchars($company['description']); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <a href="<?php echo SITE_URL; ?>/jobs.php?keyword=<?php echo urlencode($company['company_name']); ?>" 
                                   class="btn btn-primary btn-sm w-100">
                                    View Jobs
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php echo buildPagination($totalCompanies, EMPLOYERS_PER_PAGE, $page, SITE_URL . '/companies.php?' . http_build_query(array_filter($_GET, function($k) { return $k !== 'page'; }, ARRAY_FILTER_USE_KEY))); ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h3 class="empty-state-title">No Companies Found</h3>
                <p class="empty-state-text">Try adjusting your search filters</p>
                <a href="<?php echo SITE_URL; ?>/companies.php" class="btn btn-primary">Clear Filters</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Employer CTA -->
<section class="section" style="background: linear-gradient(135deg, var(--cream) 0%, var(--light-gray) 100%); color: var(--dark-charcoal);">
    <div class="container text-center">
        <h2 class="mb-3">Are You an Employer?</h2>
        <p class="mb-4" style="color: var(--text-gray);">Register your company and start hiring talented professionals</p>
        <a href="<?php echo SITE_URL; ?>/employer-register.php" class="btn btn-primary btn-lg">Register Your Company</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
