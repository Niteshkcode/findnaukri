<?php
/**
 * FindNaukri.in - Diagnostic Script
 * Run this to identify configuration issues
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>FindNaukri.in - Diagnostic Tool</h1>";
echo "<hr>";

// Check PHP version
echo "<h2>PHP Version</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
if (version_compare(phpversion(), '7.4', '<')) {
    echo "<p style='color: red;'>⚠ PHP version should be 7.4 or higher</p>";
} else {
    echo "<p style='color: green;'>✓ PHP version is compatible</p>";
}

// Check required extensions
echo "<h2>Required PHP Extensions</h2>";
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'session'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'>✓ $ext: Loaded</p>";
    } else {
        echo "<p style='color: red;'>✗ $ext: Not Loaded</p>";
    }
}

// Check .env file
echo "<h2>Environment Configuration</h2>";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "<p style='color: green;'>✓ .env file exists</p>";
    echo "<p>File path: " . $envFile . "</p>";
    echo "<p>File size: " . filesize($envFile) . " bytes</p>";
    echo "<p>Readable: " . (is_readable($envFile) ? 'Yes' : 'No') . "</p>";
} else {
    echo "<p style='color: red;'>✗ .env file not found!</p>";
    echo "<p><strong>Action Required:</strong> Upload .env file to the server</p>";
}

// Check config file
echo "<h2>Configuration Files</h2>";
$configFiles = [
    'includes/config.php',
    'includes/database.php',
    'includes/functions.php',
    'includes/auth.php'
];
foreach ($configFiles as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        echo "<p style='color: green;'>✓ $file exists</p>";
    } else {
        echo "<p style='color: red;'>✗ $file missing</p>";
    }
}

// Check database connection
echo "<h2>Database Connection</h2>";
if (file_exists($envFile)) {
    require_once __DIR__ . '/includes/config.php';
    require_once __DIR__ . '/includes/database.php';

    echo "<h3>Configuration:</h3>";
    echo "<pre>";
    echo "DB_HOST: " . DB_HOST . "\n";
    echo "DB_PORT: " . DB_PORT . "\n";
    echo "DB_NAME: " . DB_NAME . "\n";
    echo "DB_USER: " . DB_USER . "\n";
    echo "DB_CHARSET: " . DB_CHARSET . "\n";
    echo "</pre>";

    try {
        $db = getDB();
        echo "<p style='color: green;'>✓ Database connection successful!</p>";

        // Check if tables exist
        $result = $db->query("SHOW TABLES");
        $tables = $result->fetchAll(PDO::FETCH_COLUMN);

        if (empty($tables)) {
            echo "<p style='color: red;'>⚠ No tables found in database!</p>";
            echo "<p><strong>Action Required:</strong> Import database/schema.sql via phpMyAdmin</p>";
        } else {
            echo "<p style='color: green;'>✓ Found " . count($tables) . " tables</p>";
            echo "<h3>Tables:</h3>";
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>" . htmlspecialchars($table) . "</li>";
            }
            echo "</ul>";
        }

    } catch (PDOException $e) {
        echo "<p style='color: red;'>✗ Database connection failed!</p>";
        echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";

        echo "<h3>Troubleshooting:</h3>";
        echo "<ul>";
        if (strpos($e->getMessage(), 'Access denied') !== false) {
            echo "<li>Database username or password is incorrect</li>";
            echo "<li>User may not have permission to access the database</li>";
            echo "<li><strong>Action:</strong> Check database credentials in .env file</li>";
            echo "<li><strong>Action:</strong> Add user to database with ALL PRIVILEGES in cPanel</li>";
        }
        if (strpos($e->getMessage(), "Unknown database") !== false) {
            echo "<li>Database '" . DB_NAME . "' doesn't exist</li>";
            echo "<li><strong>Action:</strong> Create database in cPanel or import schema.sql</li>";
        }
        if (strpos($e->getMessage(), "Can't connect") !== false) {
            echo "<li>MySQL server is not running or unreachable</li>";
            echo "<li>Wrong host or port</li>";
            echo "<li><strong>Action:</strong> Check DB_HOST in .env (should be 'localhost' for cPanel)</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p style='color: orange;'>⚠ Cannot test database connection - .env file missing</p>";
}

// Check uploads directory
echo "<h2>Uploads Directory</h2>";
$uploadDir = __DIR__ . '/uploads';
if (file_exists($uploadDir)) {
    echo "<p style='color: green;'>✓ Uploads directory exists</p>";
    echo "<p>Writable: " . (is_writable($uploadDir) ? 'Yes' : 'No') . "</p>";
    if (!is_writable($uploadDir)) {
        echo "<p style='color: red;'>⚠ Uploads directory is not writable!</p>";
        echo "<p><strong>Action:</strong> Set permissions to 755 or 777</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠ Uploads directory doesn't exist</p>";
    echo "<p>It will be created automatically when needed</p>";
}

// Check .htaccess
echo "<h2>.htaccess File</h2>";
$htaccessFile = __DIR__ . '/.htaccess';
if (file_exists($htaccessFile)) {
    echo "<p style='color: green;'>✓ .htaccess file exists</p>";
} else {
    echo "<p style='color: orange;'>⚠ .htaccess file missing</p>";
}

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Ensure .env file is uploaded with correct database credentials</li>";
echo "<li>Import database/schema.sql via phpMyAdmin</li>";
echo "<li>Add database user to database with ALL PRIVILEGES</li>";
echo "<li>Ensure uploads directory is writable (755 or 777 permissions)</li>";
echo "<li>Delete this diagnostic.php file after fixing issues</li>";
echo "</ol>";
