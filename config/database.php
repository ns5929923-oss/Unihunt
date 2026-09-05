<?php
/**
 * Database Configuration for UniHunt
 * Centralized database connection settings
 */

class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "unihunt";
    private $charset = "utf8mb4";
    private $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            throw new PDOException("Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    public function query($sql) {
        return $this->conn->query($sql);
    }

    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
}
?>
