<?php
class Database {
   public function __construct() {
    $config = $this->getDatabaseConfig();
    
    try {
        $this->conn = new PDO($config['dsn'], $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
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
}
?>
