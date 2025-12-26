<?php
class Database {
    private static $instance = null;
    private $pdo;

    private $host = 'localhost';
    private $dbname = 'parking_db';
    private $username = 'root';
    private $password = '';

    private function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=utf8", $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}
