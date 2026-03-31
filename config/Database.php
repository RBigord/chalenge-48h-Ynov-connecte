<?php

class Database
{
    private string $host;
    private string $dbName;
    private string $user;
    private string $password;
    private static ?PDO $instance = null;
    private static ?array $envCache = null;

    public function __construct()
    {
        $this->host = $this->resolveEnv('DB_HOST', '127.0.0.1');
        $this->dbName = $this->resolveEnv('DB_NAME', 'ynov_network');
        $this->user = $this->resolveEnv('DB_USER', 'root');
        $this->password = $this->resolveEnv('DB_PASS', '');
    }

    private function resolveEnv(string $key, string $default): string
    {
        $value = getenv($key);
        if ($value !== false && $value !== '') {
            return (string) $value;
        }

        $env = self::loadEnvFiles();
        if (isset($env[$key]) && $env[$key] !== '') {
            return $env[$key];
        }

        return $default;
    }

    private static function loadEnvFiles(): array
    {
        if (self::$envCache !== null) {
            return self::$envCache;
        }

        $env = [];
        $candidates = [
            __DIR__ . '/../.env',
            __DIR__ . '/../Backend/.env',
        ];

        foreach ($candidates as $path) {
            if (!is_file($path)) {
                continue;
            }

            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($lines === false) {
                continue;
            }

            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
                    continue;
                }

                [$name, $rawValue] = explode('=', $line, 2);
                $name = trim($name);
                $rawValue = trim($rawValue);

                $value = trim($rawValue, " \t\n\r\0\x0B\"'");
                if ($name !== '') {
                    $env[$name] = $value;
                }
            }
        }

        self::$envCache = $env;
        return self::$envCache;
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
