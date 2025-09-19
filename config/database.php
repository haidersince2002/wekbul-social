<?php
// Development error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// File: config/database.php
class Database
{
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $pdo;

    public function __construct()
    {
        // Optional local override file (git-ignored) to avoid hardcoding secrets
        $local = __DIR__ . '/local.php';
        if (file_exists($local)) {
            // local.php should set DB_HOST, DB_NAME, DB_USER, DB_PASS via putenv() or $_ENV
            include $local;
        }

        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->dbname = getenv('DB_NAME') ?: 'social_network';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') ?: '';
        $this->connect();
    }

    private function connect()
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $this->pdo = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            if (ini_get('display_errors')) {
                die('Database connection failed: ' . $e->getMessage());
            }
            die('Database connection failed.');
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
