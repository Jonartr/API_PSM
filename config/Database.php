<?php
class Database {
    private $conn;
    
    public function __construct() {
        $config = $this->getDatabaseConfig();
        
        try {
            $this->conn = new PDO($config['dsn'], $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            
            // Log de conexión exitosa
            error_log("✅ Conexión exitosa a: " . $config['type']);
            
        } catch(PDOException $e) {
            $this->handleError($e->getMessage(), $config['dsn']);
        }
    }
    
    private function getDatabaseConfig() {
        // 1. PostgreSQL Railway
        if ($databaseUrl = getenv('DATABASE_URL')) {
            $params = parse_url($databaseUrl);
            return [
                'dsn' => "pgsql:host={$params['host']};port={$params['port']};dbname=" . ltrim($params['path'], '/'),
                'username' => $params['user'],
                'password' => $params['pass'],
                'type' => 'postgresql'
            ];
        }
        
        // 2. MySQL Railway
        if ($mysqlHost = getenv('MYSQLHOST')) {
            return [
                'dsn' => "mysql:host=$mysqlHost;port=" . (getenv('MYSQLPORT') ?: '3306') . ";dbname=" . getenv('MYSQLDATABASE') . ";charset=utf8mb4",
                'username' => getenv('MYSQLUSER'),
                'password' => getenv('MYSQLPASSWORD'),
                'type' => 'mysql'
            ];
        }
        
        // 3. MySQL Local (fallback)
        return [
            'dsn' => "mysql:host=localhost;dbname=tu_db;charset=utf8mb4",
            'username' => "root",
            'password' => "",
            'type' => 'mysql_local'
        ];
    }
    
    // MÉTODO handleError QUE FALTABA
    private function handleError($message, $dsn = null) {
        // Log detallado para debugging
        error_log("❌ DATABASE ERROR: " . $message);
        if ($dsn) {
            error_log("📡 DSN: " . $dsn);
        }
        
        // No mostrar detalles sensibles en producción
        $showDetails = getenv('RAILWAY_ENVIRONMENT') !== 'production';
        
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => $showDetails ? $message : "Error de conexión a la base de datos",
            "error_type" => "database_connection_failed"
        ]);
        exit();
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    // Método para verificar conexión
    public function testConnection() {
        try {
            if ($this->conn) {
                $stmt = $this->conn->query("SELECT 1 as test");
                $result = $stmt->fetch();
                return $result && $result['test'] == 1;
            }
            return false;
        } catch (Exception $e) {
            error_log("❌ Test conexión falló: " . $e->getMessage());
            return false;
        }
    }
    
    // Método para obtener info de la BD
    public function getDatabaseInfo() {
        try {
            if ($this->conn) {
                if (getenv('DATABASE_URL')) {
                    // PostgreSQL
                    $stmt = $this->conn->query("SELECT version() as version, current_database() as db_name");
                } else {
                    // MySQL
                    $stmt = $this->conn->query("SELECT version() as version, database() as db_name");
                }
                return $stmt->fetch();
            }
            return ["error" => "No hay conexión"];
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
    
    // Método para inicializar tablas si no existen
    public function initTables() {
        try {
            // Crear tabla favorites si no existe
            $createTableSQL = "
            CREATE TABLE IF NOT EXISTS favorites (
                id SERIAL PRIMARY KEY,
                user_id VARCHAR(255) NOT NULL,
                item_id VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(user_id, item_id)
            )";
            
            $this->conn->exec($createTableSQL);
            error_log("✅ Tabla 'favorites' creada/verificada");
            
            return true;
        } catch (Exception $e) {
            error_log("❌ Error creando tabla: " . $e->getMessage());
            return false;
        }
    }
}
?>
