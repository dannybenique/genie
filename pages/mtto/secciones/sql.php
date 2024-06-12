<?php
  include_once('../../../includes/sess_verifica.php');
  include_once('../../../includes/db_database.php');
  include_once('../../../includes/web_config.php');
  include_once('../../../includes/funciones.php');

  if (!isset($_SESSION["usr_ID"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Caducó la sesión.")); }
  if (!isset($_REQUEST["appSQL"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Ninguna variable en POST")); }
  
  $data = json_decode($_REQUEST['appSQL']);
  $rpta = 0;
  
  function obtenerColNiv($nivelID){
    $web = $GLOBALS["web"];
    $db = $GLOBALS["db"];
    $params = [
      ":nivelID" => $nivelID,
      ":colegioID" => $web->colegioID
    ];

    //niveles por colegio
    $colniv = array();
    $sql = "select * from vw_colniv where id_colegio=:colegioID and id_nivel=:nivelID order by id_nivel,id_grado,seccion;";
    $qry = $db->query_all($sql,$params);
    if ($qry) {
      foreach($qry as $rs){
        $colniv[] = array(
          "nivelID" => $rs["id_nivel"],
          "nivel" => $rs["nivel"],
          "gradoID" => $rs["id_grado"],
          "grado" => $rs["grado"],
          "seccionID" => $rs["id_seccion"],
          "seccion" => $rs["seccion"],
          "alias" => $rs["alias"],
          "capacidad" => $rs["capacidad"]
        );
      }
    }
    return $colniv;
  }

  switch ($data->TipoQuery) {
    case "nivel_select":
      //respuesta
      $rpta = array("colniv"=>obtenerColNiv($data->nivelID));
      $db->enviarRespuesta($rpta);
      break;
    case "nivel_refresh":
      //respuesta
      $rpta = array("niveles"=>obtenerNiveles($data->nivelID));
      $db->enviarRespuesta($rpta);
      break;
    case "nivel_start":
      //respuesta
      $rpta = array(
        "comboNiveles" => $fn->getComboBox("select distinct n.id,n.nombre from app_niveles n join app_niveles g on g.id_padre = n.id join app_niveles s on s.id_padre = g.id join app_colniv x on s.id=x.id_nivel where id_colegio=".$web->colegioID." order by id;"),
        "colegio" => $web->colegioID);
      $db->enviarRespuesta($rpta);
      break;
    case "colniv_edit":
      $sql = "select * from vw_colniv where id_seccion=:seccionID and id_colegio=:colegioID;";
      $params = [
        ":seccionID"=>$data->nivelID,
        ":colegioID"=>$web->colegioID
      ];
      $qry = $db->query_all($sql,$params);
      if ($qry) {
        $rs = reset($qry);
        $rpta = array(
          "seccionID" => $rs["id_seccion"],
          "nivel" => $rs["nivel"],
          "grado" => ($rs["grado"]),
          "seccion" => ($rs["seccion"]),
          "alias" => ($rs["alias"]),
          "capacidad" => ($rs["capacidad"])
        );
      }

      //respuesta
      $db->enviarRespuesta($rpta);
      break;
    case "colniv_update":
      $sql = "update app_colniv set alias=:alias,capacidad=:capacidad where id_nivel=:seccionID and id_colegio=:colegioID";
      $params = [
        ":alias"=>$data->alias,
        ":capacidad"=>$data->capacidad,
        ":seccionID"=>$data->seccionID,
        ":colegioID"=>$web->colegioID
      ];
      $qry = $db->query_all($sql,$params);
      $rs = ($qry) ? (reset($qry)) : (null);

      //respuesta
      $rpta = array("error" => false, "colniv"=>obtenerColNiv($data->nivelID));
      $db->enviarRespuesta($rpta);
      break;
    case "colniv_refresh":
      //respuesta
      $rpta = array("colniv"=>obtenerColNiv($data->nivelID));
      $db->enviarRespuesta($rpta);
      break;
    case "colniv_delete":
      for($i=0; $i<count($data->arr); $i++){
        $sql = "delete from app_colniv where id_nivel=:nivelID and id_colegio=:colegioID";
        $params = [
          ":nivelID"=>$data->arr[$i],
          ":colegioID"=>$web->colegioID
        ];
        $qry = $db->query_all($sql,$params);
        $rs = ($qry) ? (reset($qry)) : (null);
      }

      //respuesta
      $rpta = array("error" => false,"borrados" => count($data->arr));
      $db->enviarRespuesta($rpta);
      break;
  }
  $db->close();
?>
