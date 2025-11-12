<?php
 
class Database {
    // Configuración para InfinityFree
    private $host = "localhost"; // El host que te den
    private $db_name = "psm"; // Tu nombre de BD
    private $username = "root"; // Tu usuario
    private $password = ""; // Tu password
    public $conn;

   
    public function getConnection() {
 
        $this->conn = null;
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            
            if ($this->conn->connect_error) {
                // En InfinityFree, a veces hay que reconectar
                error_log("Database connection error: " . $this->conn->connect_error);
                return null;
            }
            
            $this->conn->set_charset("utf8");
            
        } catch(Exception $e) {
            error_log("Database exception: " . $e->getMessage());
            return null;
        }
        return $this->conn;
    }
}
?>