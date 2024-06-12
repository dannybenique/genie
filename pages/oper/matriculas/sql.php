<?php
  include_once('../../../includes/sess_verifica.php');
  include_once('../../../includes/db_database.php');
  include_once('../../../includes/funciones.php');
  include_once('../../../includes/web_config.php');

  if (!isset($_SESSION["usr_ID"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Caducó la sesión.")); }
  if (!isset($_REQUEST["appSQL"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Ninguna variable en POST")); }
  
  $data = json_decode($_REQUEST['appSQL']);
  $rpta = 0;

  switch ($data->TipoQuery) {
    case "matricula_Select":
      $whr = "";
      $tabla = array();
      $buscar = strtoupper($data->buscar);
      $whr = " and id_colegio=:colegioID and (alumno LIKE :buscar or nro_dui LIKE :buscar) ";
      $params = [":colegioID"=>$web->colegioID,":buscar"=>'%'.$buscar.'%'];
      $qry = $db->query_all("select count(*) as cuenta from vw_matriculas_state1 where estado=1 ".$whr.";",$params);
      $rsCount = reset($qry);

      $qry = $db->query_all("select * from vw_matriculas_state1 where estado=1 ".$whr." order by alumno limit 25 offset 0;",$params);
      if($qry) {
        foreach($qry as $rs){
          $tabla[] = array(
            "ID" => $rs["id"],
            "codigo" => $rs["codigo"],
            "yyyy" => $rs["yyyy"],
            "fecha" => $rs["fecha_matricula"],
            "nro_dui"=> str_replace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["nro_dui"]),
            "alumno" => str_ireplace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["alumno"]),
            "nivel" => $rs["nivel"],
            "grado" => $rs["grado"],
            "seccion" => $rs["seccion"],
            "importe" => $rs["importe"]*1,
            "saldo" => $rs["saldo"]*1,
            "nro_cuotas" => $rs["nro_cuotas"]
          );
        }
      }

      //respuesta
      $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount["cuenta"]);
      $db->enviarRespuesta($rpta);
      break;
    case "matricula_View": //visualiza el credito de la matricula
      //cabecera
      $cabecera = 0;
      $qry = $db->query_all("select * from vw_matriculas_state1 where id=:id",[":id"=>$data->matriculaID]);
      if($qry) {
        $rs = reset($qry);
        
        $cabecera = array(
          "ID" => $rs["id"],
          "alumno" => $rs["alumno"],
          "nro_dui" => $rs["nro_dui"],
          "codigo" => $rs["codigo"],
          "fechaMatricula" => $rs["fecha_matricula"],
          "fechaAprueba" => $rs["fecha_aprueba"],
          "fechaSolicita" => $rs["fecha_solicita"],
          "yyyy" => $rs["yyyy"],
          "nivel" => $rs["nivel"],
          "grado" => $rs["grado"],
          "seccion" => $rs["seccion"],
          "importe" => $rs["importe"],
          "saldo" => $rs["saldo"],
          "estado" => $rs["estado"]
        );
      }

      //detalle
      $detalle = array();
      $sql = "select d.*,p.nombre as producto,current_date-vencimiento as atraso from app_matriculas_det d join app_productos p on p.id=d.id_producto where d.id_matricula=:matriculaID order by item;";
      $params = ["matriculaID" => $data->matriculaID];
      $qry = $db->query_all($sql,$params);
      if ($qry) {
        foreach($qry as $rs){
          $detalle[] = array(
            "id" => $rs["id"],
            "item" => $rs["item"],
            "producto" => $rs["producto"],
            "importe" => $rs["importe"]*1,
            "saldo" => $rs["saldo"]*1,
            "vencimiento" => $rs["vencimiento"],
            "atraso" => $rs["atraso"]
          );
        }
      }
      //respuesta
      $rpta = array('cabecera'=>$cabecera, "detalle"=>$detalle);
      $db->enviarRespuesta($rpta);
      break;
  }
  $db->close();
?>
