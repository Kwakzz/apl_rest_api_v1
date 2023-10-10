<?php
class Database {
    private $host = "localhost";
    private $database_name = "apl";
    private $username = "apladmin";
    private $password = "apladminpasswd";
    private $dsn;
    public $conn;

    public function __construct() {
        $this->dsn = "mysql:host={$this->host};dbname={$this->database_name};charset=utf8mb4";
    }

    /**
     * Get the database connection using PDO
     */
    public function getConnection() {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO($this->dsn, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $exception) {
                error_log("Database connection error: " . $exception->getMessage());
                die("Database connection error: " . $exception->getMessage());
            }
        }
        return $this->conn;
    }

    /**
     * Get the database connection using MySQLi
     * This is not used in this project
     */
    // public function getConnection() {
           
    //     $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database_name);
    //     if ($this->conn->connect_error) {
    //         die("Connection failed: " . $this->conn->connect_error);
    //     }     
    //     return $this->conn;
    // }
}
?>
