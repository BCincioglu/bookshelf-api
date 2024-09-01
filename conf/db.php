<?php
    class Database {
        private $host;
        private $db;
        private $user;
        private $pass;
        private $charset;
        private $pdo;

        public function __construct($isTest = false) {
            if ($isTest) {
                $this->host = $_ENV['TEST_DB_HOST'] ?? 'localhost';
                $this->db = $_ENV['TEST_DB_NAME'] ?? 'bookshelf_test';
                $this->user = $_ENV['TEST_DB_USER'] ?? 'root';
                $this->pass = $_ENV['TEST_DB_PASS'] ?? 'YeniSifreniz';
            } else {
                $this->host = $_ENV['DB_HOST'] ?? 'localhost';
                $this->db = $_ENV['DB_NAME'] ?? 'bookshelf';
                $this->user = $_ENV['DB_USER'] ?? 'root';
                $this->pass = $_ENV['DB_PASS'] ?? 'YeniSifreniz';
            }
            
            $this->charset = 'utf8mb4';
        }

        public function getConnection() {
            if ($this->pdo === null) {
                $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];

                try {
                    $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
                } catch (\PDOException $e) {
                    error_log($e->getMessage());
                    throw new \PDOException('Database connection error', (int)$e->getCode());
                }
            }

            return $this->pdo;
        }
    }

?>