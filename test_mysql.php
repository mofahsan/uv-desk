<?php
// MySQL connection test
$connections = [
    // Connection using container name (for container-to-container communication)
    [
        'host' => 'uvdesk_mysql',
        'port' => 3306,
        'database' => 'uvdesk',
        'username' => 'uvdesk',
        'password' => 'uvdesk'
    ],
    // Connection using docker network alias
    [
        'host' => 'mysql',
        'port' => 3306,
        'database' => 'uvdesk',
        'username' => 'uvdesk',
        'password' => 'uvdesk'
    ],
    // Connection using root user
    [
        'host' => 'uvdesk_mysql',
        'port' => 3306,
        'database' => 'uvdesk',
        'username' => 'root',
        'password' => 'uvdesk'
    ]
];

echo "Testing MySQL connections...\n\n";

foreach ($connections as $index => $config) {
    echo "Test " . ($index + 1) . ": Connecting to {$config['host']}:{$config['port']}\n";
    echo "Database: {$config['database']}, User: {$config['username']}\n";
    
    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Test the connection
        $result = $pdo->query("SELECT VERSION() as version, DATABASE() as db")->fetch(PDO::FETCH_ASSOC);
        
        echo "✓ SUCCESS! Connected to MySQL\n";
        echo "  MySQL Version: " . $result['version'] . "\n";
        echo "  Current Database: " . $result['db'] . "\n";
        echo "  Working connection string:\n";
        echo "  DSN: $dsn\n";
        echo "  Username: {$config['username']}\n";
        echo "  Password: {$config['password']}\n";
        
        // Test creating a simple table
        $pdo->exec("CREATE TABLE IF NOT EXISTS test_connection (id INT AUTO_INCREMENT PRIMARY KEY, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
        echo "  ✓ Table creation test successful\n";
        
        // Clean up
        $pdo->exec("DROP TABLE IF EXISTS test_connection");
        
    } catch (PDOException $e) {
        echo "✗ FAILED: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// Also test mysqli extension
echo "Testing MySQLi extension...\n";
if (extension_loaded('mysqli')) {
    echo "✓ MySQLi extension is loaded\n";
    
    $mysqli = @new mysqli('uvdesk_mysql', 'uvdesk', 'uvdesk', 'uvdesk', 3306);
    if ($mysqli->connect_errno) {
        echo "✗ MySQLi connection failed: " . $mysqli->connect_error . "\n";
    } else {
        echo "✓ MySQLi connection successful\n";
        $mysqli->close();
    }
} else {
    echo "✗ MySQLi extension is not loaded\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "RECOMMENDED CONNECTION STRINGS:\n";
echo str_repeat("=", 50) . "\n\n";

echo "PDO Connection String:\n";
echo '$dsn = "mysql:host=uvdesk_mysql;port=3306;dbname=uvdesk;charset=utf8mb4";' . "\n";
echo '$pdo = new PDO($dsn, "uvdesk", "uvdesk");' . "\n\n";

echo "MySQLi Connection String:\n";
echo '$mysqli = new mysqli("uvdesk_mysql", "uvdesk", "uvdesk", "uvdesk", 3306);' . "\n\n";

echo "Environment Variables (for .env file):\n";
echo "DB_HOST=uvdesk_mysql\n";
echo "DB_PORT=3306\n";
echo "DB_DATABASE=uvdesk\n";
echo "DB_USERNAME=uvdesk\n";
echo "DB_PASSWORD=uvdesk\n";
?>