<?php
  if (isset($_GET["start"])){
    include_once('../../../includes/db_database.php');
    $ini = $_GET["start"];
    $dia = (int) substr($ini,8,2);
    $mes = (int) substr($ini,5,2);
    $yyy = (int) substr($ini,0,4);
    $rpta = 0;

    if($dia>20) {$mes++;}
    if($mes>12) {$mes=1;$yyy++;}
    $sql = "select p.nombres+' '+p.ap_paterno+' '+p.ap_materno as worker,w.nombrecorto,'".$yyy."'+'-'+format(p.fecha_nac,'MM-dd') as fecha,DAY(p.fecha_nac) as dia from dbo.tb_workers w,dbo.tb_personas p where p.ID=w.id_persona and w.estado=1 and MONTH(p.fecha_nac)=".$mes." order by dia";
    $qry = $db->select($sql);
    if ($db->has_rows($qry)) {
      $eventos = array();
      for($xx = 0; $xx<$db->num_rows($qry); $xx++){
        $rs = $db->fetch_array($qry);
        $eventos[] = array(
          "title"=>utf8_encode($rs["nombrecorto"]),
          "start" => $rs["fecha"],
          "end" => $rs["fecha"]
        );
      }
    }
    //print_r(json_encode($eventos,JSON_UNESCAPED_UNICODE));
    header('Content-Type: application/json');
    echo json_encode($eventos);
    //var_dump($sql);

    $db->close();
  } else{
    $resp = array("error"=>true,"resp"=>"ninguna variable en GET");
    header('Content-Type: application/json');
    echo json_encode($resp);
  }
?>
