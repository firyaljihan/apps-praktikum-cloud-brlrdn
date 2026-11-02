<?php
require_once __DIR__ . '/../config/database.php';

class Database {
    private $conn;

    public function __construct() {
        $dir = dirname(DB_PATH);

        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $this->conn = new PDO('sqlite:' . DB_PATH);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->conn->exec("
            CREATE TABLE IF NOT EXISTS produk (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nama TEXT NOT NULL,
                deskripsi TEXT,
                harga REAL NOT NULL,
                foto TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>
