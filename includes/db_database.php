<?php
  class database{
    private static $instance = null;
    private $conn;

    private function __construct(){ //constructor
      try {
        $host   = "129.213.27.61";
        $port   = "5432";
        $dbname = "genie";
        $user   = "postgres";
        $pass   = "1pharr0w.";
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        
        $this->conn = new PDO($dsn, $user, $pass);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
      }
    }

    public static function getInstance() {
      if (self::$instance===null) { self::$instance = new database(); }
      return self::$instance;
    }
    
    public function query_all($sql, $params = []) {
      try {
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
          if (is_string($value) && strpos($sql, $key) !== false) {
              $stmt->bindParam($key, $params[$key], PDO::PARAM_STR);
          } else {
              $stmt->bindParam($key, $params[$key]);
          }
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
      } catch (PDOException $e) {
        die("Error en la consulta: " . $e->getMessage());
      }
    }

    public function close() { $this->conn = null; }
    
    public function enviarRespuesta($resp){
      header('Content-Type: application/json');
      echo json_encode($resp);
      exit;
    }
  }
  $db = database::getInstance();
?>
