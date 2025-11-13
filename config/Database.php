<?php
class Database {
    private $conn;
    
    public function __construct() {
        $config = $this->getDatabaseConfig();
        
        // Log de configuración
        error_log("🎯 Configuración BD: " . $config['type']);
        
        try {
            $this->conn = new PDO($config['dsn'], $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 30,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
            
            error_log("✅ Conexión MySQL exitosa a: " . $config['host']);
            
        } catch(PDOException $e) {
            $this->handleError($e->getMessage(), $config);
        }
    }
    
    private function getDatabaseConfig() {
        // 1. MySQL RAILWAY (PRIMERA OPCIÓN)
        if ($mysqlHost = getenv('DB_HOST')) {
            error_log("🎯 Usando MySQL Railway");
            
            return [
                'dsn' => "mysql:host=" . getenv('DB_HOST') . 
                         ";port=" . (getenv('DB_PORT') ?: '3306') . 
                         ";dbname=" . getenv('DB_DATABASE') . 
                         ";charset=utf8mb4",
                'username' => getenv('DB_USER') ?: '',
                'password' => getenv('DB_PASSWORD') ?: '',
                'type' => 'mysql_railway',
                'host' => getenv('DB_HOST'),
                'database' => getenv('DB_DATABASE')
            ];
        }
        
        // 2. MySQL LOCAL (FALLBACK - DESARROLLO)
        error_log("⚠️ Usando MySQL Local (FALLBACK)");
        return [
            'dsn' => "mysql:host=localhost;dbname=tu_db;charset=utf8mb4",
            'username' => "root",
            'password' => "",
            'type' => 'mysql_local',
            'host' => 'localhost',
            'database' => 'tu_db'
        ];
    }
    
    private function handleError($message, $config) {
        error_log("❌ DATABASE ERROR: " . $message);
        error_log("📡 Config Type: " . $config['type']);
        error_log("🔗 DSN: " . ($config['dsn'] ?? 'No DSN'));
        
        $showDetails = true; // Mostrar detalles para debugging
        
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => $showDetails ? $message : "Error de conexión a la base de datos",
            "error_type" => "database_connection_failed",
            "config_type" => $config['type'],
            "debug_info" => $showDetails ? [
                "mysql_host" => getenv('DB_HOST') ?: 'NOT_SET',
                "mysql_database" => getenv('DB_DATABASE') ?: 'NOT_SET',
                "mysql_user" => getenv('DB_USER') ?: 'NOT_SET',
                "mysql_port" => getenv('DB_PORT') ?: '3306'
            ] : 'hidden'
        ]);
        exit();
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    // Método para verificar conexión
    public function testConnection() {
        try {
            $stmt = $this->conn->query("SELECT 1 as test, NOW() as server_time");
            $result = $stmt->fetch();
            return [
                "connected" => true,
                "server_time" => $result['server_time'],
                "mysql_version" => $this->conn->getAttribute(PDO::ATTR_SERVER_VERSION)
            ];
        } catch (Exception $e) {
            error_log("❌ Test conexión falló: " . $e->getMessage());
            return ["connected" => false, "error" => $e->getMessage()];
        }
    }
    

    
    // Método para obtener información de la BD
    public function getDatabaseInfo() {
        try {
            $stmt = $this->conn->query("
                SELECT 
                    VERSION() as mysql_version,
                    DATABASE() as database_name,
                    NOW() as server_time,
                    @@hostname as hostname
            ");
            return $stmt->fetch();
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
}
?>
