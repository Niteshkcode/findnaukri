<?php
/**
 * Database Connection Test Script
 * Run this to verify database connection
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';

echo "<h2>Database Connection Test</h2>";
echo "<hr>";

echo "<h3>Configuration:</h3>";
echo "<pre>";
echo "DB_HOST: " . DB_HOST . "\n";
echo "DB_PORT: " . DB_PORT . "\n";
echo "DB_NAME: " . DB_NAME . "\n";
echo "DB_USER: " . DB_USER . "\n";
echo "DB_CHARSET: " . DB_CHARSET . "\n";
echo "</pre>";

echo "<h3>Testing Connection...</h3>";
try {
    $db = getDB();
    echo "<p style='color: green;'>✓ Database connection successful!</p>";

    // Test a simple query
    $result = $db->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "'");
    $row = $result->fetch();
    echo "<p>Tables in database: " . $row['count'] . "</p>";

    // List tables
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    if (empty($tables)) {
        echo "<p style='color: orange;'>⚠ No tables found. You need to import the schema.</p>";
    } else {
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

    // Common issues
    echo "<h3>Common Issues:</h3>";
    echo "<ul>";
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "<li>Database username or password is incorrect</li>";
        echo "<li>User doesn't have permission to access the database</li>";
    }
    if (strpos($e->getMessage(), "Unknown database") !== false) {
        echo "<li>Database '" . DB_NAME . "' doesn't exist</li>";
    }
    if (strpos($e->getMessage(), "Can't connect") !== false) {
        echo "<li>MySQL server is not running</li>";
        echo "<li>Wrong host or port</li>";
    }
    echo "</ul>";
}
