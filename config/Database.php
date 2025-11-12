<?php
class Database {
    private $conn;
    
    public function __construct() {
        // CONFIGURACIÓN PARA RAILWAY (PostgreSQL)
        $databaseUrl = getenv('DATABASE_URL');
        
        if ($databaseUrl) {
            // PostgreSQL en Railway
            $dbParams = parse_url($databaseUrl);
            $host = $dbParams['host'];
            $port = $dbParams['port'] ?? '5432';
            $dbname = ltrim($dbParams['path'], '/');
            $username = $dbParams['user'];
            $password = $dbParams['pass'];
            
            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        } else {
            // MySQL para desarrollo local
            $dsn = "mysql:host=localhost;dbname=tu_db;charset=utf8mb4";
            $username = "root";
            $password = "";
        }
        
        try {
            $this->conn = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch(PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Error de conexión a la base de datos"
            ]);
            exit();
        }
    }
    
    public function getConnection() {
        return $this->conn;
    }
}
?>