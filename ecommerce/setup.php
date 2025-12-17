<?php
/**
 * Database Setup Script
 * Run this file once to create the database and tables
 * Access: http://localhost/ecommerce/setup.php
 */

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'ecommerce_db';

try {
    // Connect to MySQL without selecting database
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Setup</h2>";
    
    // Create database
    echo "<p>Creating database '$dbname'...</p>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p style='color: green;'>✓ Database created successfully</p>";
    
    // Select database
    $pdo->exec("USE `$dbname`");
    
    // Read and execute schema file
    echo "<p>Importing schema...</p>";
    $schemaFile = __DIR__ . '/database/schema.sql';
    
    if (!file_exists($schemaFile)) {
        throw new Exception("Schema file not found: $schemaFile");
    }
    
    $sql = file_get_contents($schemaFile);
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $successCount = 0;
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        try {
            $pdo->exec($statement);
            $successCount++;
        } catch (PDOException $e) {
            echo "<p style='color: orange;'>Warning: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    
    echo "<p style='color: green;'>✓ Executed $successCount SQL statements</p>";
    
    // Verify tables
    echo "<h3>Verifying Tables:</h3>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li style='color: green;'>✓ $table</li>";
    }
    echo "</ul>";
    
    echo "<h3 style='color: green;'>✅ Setup Complete!</h3>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ul>";
    echo "<li>Delete this setup.php file for security</li>";
    echo "<li>Visit: <a href='index.php'>Homepage</a></li>";
    echo "<li>Admin Login: <a href='admin/index.php'>Admin Panel</a> (admin@marketplace.com / admin123)</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Troubleshooting:</strong></p>";
    echo "<ul>";
    echo "<li>Make sure XAMPP MySQL is running</li>";
    echo "<li>Check database credentials in this file</li>";
    echo "<li>Verify you have permission to create databases</li>";
    echo "</ul>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Setup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h2, h3 {
            color: #333;
        }
        ul {
            background: white;
            padding: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
</body>
</html>
