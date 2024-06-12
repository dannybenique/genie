<?php
  include_once('../../../includes/db_database.php');
  include_once('../../../includes/funciones.php');

  if (!isset($_REQUEST["start"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Ninguna variable en GET")); }

  $ini = $_GET["start"];
  $dia = (int) substr($ini,8,2);
  $mes = (int) substr($ini,5,2);
  $yyy = (int) substr($ini,0,4);
  $rpta = 0;
  
  if($dia>20) { $mes++; }
  if($mes>12) { $mes=1;$yyy++; }
  
  $sql = "select p.nombres+' '+p.ap_paterno+' '+p.ap_materno as worker,w.nombrecorto,'".$yyy."'+'-'+format(p.fecha_nac,'MM-dd') as fecha,DAY(p.fecha_nac) as dia from dbo.tb_workers w,dbo.tb_personas p where p.ID=w.id_persona and w.estado=1 and MONTH(p.fecha_nac)=".$mes." order by dia";
  $qry = $db->query_all($sql);
  if ($qry) {
    $eventos = array();
    foreach($qry as $rs){
      $eventos[] = array(
        "title"=>$rs["nombrecorto"],
        "start" => $rs["fecha"],
        "end" => $rs["fecha"]
      );
    }
  }
  //print_r(json_encode($eventos,JSON_UNESCAPED_UNICODE));
  $db->enviarRespuesta($eventos);
  $db->close();
?>
