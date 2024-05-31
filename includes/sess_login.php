<?php
  function iniciarSesion($usuarios) {
    // En este punto, el usuario ya esta validado. Grabamos los datos del usuario en una sesion.
    session_cache_limiter('nocache,private');
    session_name("GENIE");
    session_start();

    foreach ($usuarios as $usuario) {
      // Asignamos variables de sesion con datos del Usuario para el uso en el resto de paginas autentificadas.
      $user = [
          "ID" => $usuario['id_usuario'],
          "login" => $usuario['login'],
          "cargo" => $usuario['cargo'],
          "rolID" => $usuario['id_rol'],
          "rolROOT" => 101,
          "colegioID" => $usuario['id_colegio'],
          "nombrecorto" => $usuario['nombrecorto'],
          "urlfoto" => (strlen($usuario['urlfoto']) > 3) ? $usuario['urlfoto'] : 'data/personas/fotouser.jpg',
          "menu" => $usuario['menu'],
      ];
      $_SESSION['usr_ID'] = $usuario['id_usuario'];
      $_SESSION['usr_data'] = $user;
    }
  }
  function enviarRespuesta($resp) {
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit;
  }

  if (isset($_POST["frmLogin"])){
    $rpta = "";
    try{
      include_once('db_database.php');
      include_once('web_config.php');
      $data = json_decode($_POST['frmLogin']);
      $params = [
        ':dblogin' => $data->login,
        ':dbpassw' => $data->passw,
        ':dbcolegio' => $web->colegioID // el 1=representa al colegio de este site
      ]; 
      $sql = "select * from vw_usuarios where login=:dblogin and passw=:dbpassw and id_colegio in(1,:dbcolegio)";
      $qry = $db->query_all($sql,$params);
      if($qry){
        iniciarSesion($qry);
        $rpta = array("error" => false);
      } else {
        $rpta = array("error" => true, "data" => "credenciales sin acceso");
      }
      enviarRespuesta($rpta);
    } catch(PDOException $e){
      enviarRespuesta(["error" => true, "data" => $e->getMessage()]);
    } finally{
      $db->close();
    }
  } else{
    enviarRespuesta(["error" => true, "data" => "Ninguna variable en POST"]);
  }
?>
