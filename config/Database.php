<?php

class Database
{
    private string $host;
    private string $dbName;
    private string $user;
    private string $password;
    private static ?PDO $instance = null;

    public function __construct()
    {
        $this->host = getenv('DB_HOST') ?: '127.0.0.1';
        $this->dbName = getenv('DB_NAME') ?: 'ynov_connecte';
        $this->user = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') ?: '';
    }

    public function connect(): PDO
    {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $this->host, $this->dbName);

        return new PDO(
            $dsn,
            $this->user,
            $this->password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    // Compatibility helper for code that expects singleton access.
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $database = new self();
            self::$instance = $database->connect();
        }

        return self::$instance;
    }
}
