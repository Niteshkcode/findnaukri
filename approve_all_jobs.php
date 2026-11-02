<?php
/**
 * Approve All Pending Jobs Script
 * Updates all pending jobs to approved status
 * DELETE THIS FILE AFTER USE!
 */

require_once 'includes/config.php';
require_once 'includes/database.php';

echo "<h1>Approve All Pending Jobs</h1>";
echo "<hr>";

// Count pending jobs
$pendingQuery = "SELECT COUNT(*) as count FROM jobs WHERE approval_status = 'pending'";
$pendingResult = fetchOne($pendingQuery);
$pendingCount = $pendingResult['count'];

echo "<p>Pending jobs found: <strong>$pendingCount</strong></p>";

if ($pendingCount > 0) {
    // Update all pending jobs to approved
    $updateQuery = "UPDATE jobs SET approval_status = 'approved' WHERE approval_status = 'pending'";

    if (execute($updateQuery)) {
        echo "<p style='color: green; font-size: 1.2rem;'>✓ All $pendingCount pending jobs have been approved!</p>";

        // Show updated jobs
        $jobsQuery = "SELECT id, title, employer_id FROM jobs WHERE approval_status = 'approved' ORDER BY posted_at DESC LIMIT 10";
        $jobs = fetchAll($jobsQuery);

        echo "<h3>Recently Approved Jobs:</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Title</th><th>Employer ID</th></tr>";
        foreach ($jobs as $job) {
            echo "<tr>";
            echo "<td>" . $job['id'] . "</td>";
            echo "<td>" . htmlspecialchars($job['title']) . "</td>";
            echo "<td>" . $job['employer_id'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>✗ Failed to update jobs</p>";
    }
} else {
    echo "<p>No pending jobs to approve.</p>";
}

// Show total approved jobs
$approvedQuery = "SELECT COUNT(*) as count FROM jobs WHERE approval_status = 'approved'";
$approvedResult = fetchOne($approvedQuery);
$approvedCount = $approvedResult['count'];

echo "<hr>";
echo "<p>Total approved jobs: <strong>$approvedCount</strong></p>";
echo "<p style='color: red;'><strong>IMPORTANT:</strong> Delete this file (approve_all_jobs.php) from the server immediately!</p>";
?>
