<?php
/**
 * Database Configuration & Connection Provider
 *
 * Uses PDO with UTF-8 support and prepared statements.
 * Reads configuration from environment variables if present, or falls back to local configuration.
 */

// Load local config file if exists (for credentials without committing to Git)
$localConfigFile = __DIR__ . '/config.php';
$config = [];
if (file_exists($localConfigFile)) {
    $config = require $localConfigFile;
}

$dbHost = getenv('DB_HOST') ?: ($config['host'] ?? '127.0.0.1');
$dbPort = getenv('DB_PORT') ?: ($config['port'] ?? '3306');
$dbName = getenv('DB_NAME') ?: ($config['dbname'] ?? 'product_catalog');
$dbUser = getenv('DB_USER') ?: ($config['user'] ?? 'root');
$dbPass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($config['pass'] ?? '');

function getDbConnection() {
    global $dbHost, $dbPort, $dbName, $dbUser, $dbPass;

    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
        } catch (PDOException $e) {
            // Log error internally; never expose DB credentials or stack trace to client
            error_log('Database Connection Error: ' . $e->getMessage());
            return null;
        }
    }

    return $pdo;
}
