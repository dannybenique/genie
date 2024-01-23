<?php
  class database{
    private static $instance;
    private $conn;

    private function __construct(){ //constructor
      $host   = "129.213.27.61";
      $port   = "5432";
      $dbname = "genie";
      $user   = "postgres";
      $pass   = "Perikl3$.";
      try {
        $this->conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
      }
    }

    public static function getInstance() {
      if (!self::$instance) { self::$instance = new database(); }
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

    public function queryXX($sql, $params = []) {
      try {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
      } catch (PDOException $e) {
        die("Error en la consulta: " . $e->getMessage());
      }
    }

    //comandos SQL
    public function query_params($sql,$params) { return pg_query_params($this->conn,$sql,$params); }
    public function send_query($sql){ return pg_send_query($this->conn,$sql); }
    public function fetch_array($rs){ return pg_fetch_array($rs); }
    public function get_result(){ return pg_get_result($this->conn); }
    public function result_error_field($rs){ return pg_result_error_field($rs,PGSQL_DIAG_SQLSTATE); }
    public function num_rows($rs){ return pg_num_rows($rs);   }
    public function get_encoding(){return pg_client_encoding($this->conn);}
    public function close() { $this->conn = null; }
  }
  $db = database::getInstance();
?>
