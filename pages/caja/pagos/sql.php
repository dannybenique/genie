<?php
  include_once('../../../includes/sess_verifica.php');
  include_once('../../../includes/db_database.php');
  include_once('../../../includes/web_config.php');
  include_once('../../../includes/funciones.php');
  
  if (!isset($_SESSION["usr_ID"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Caducó la sesión.")); }
  if (!isset($_REQUEST["appSQL"])) { $db->enviarRespuesta(array("error" => true, "mensaje" => "Ninguna variable en POST")); }
  
  $data = json_decode($_REQUEST['appSQL']);
  $rpta = 0;

  function pago_Item($item,$importe){
    return ($item>0) ? (round(min($item,$importe),2)):(0);
  }

  switch ($data->TipoQuery) {
    case "matricula_select":
      $tabla = array();
      $buscar = strtoupper($data->buscar);
      $params = [":colegioID"=>$web->colegioID,":buscar"=>'%'.$buscar.'%'];
      $sql = "select p.id as id_alumno,p.nro_dui,fn_get_persona(p.tipo_persona, p.ap_paterno, p.ap_materno, p.nombres) AS alumno,m.id as id_matricula,m.fecha_matricula,m.codigo,m.importe,m.saldo,m.yyyy,n.nivel,n.grado,n.seccion,d.id_producto,d.saldo as saldo_det,extract(days from (now()-d.vencimiento)) as atraso from personas p join app_matriculas m on(m.id_alumno=p.id) join vw_niveles n on(m.id_seccion=n.id_seccion) join app_matriculas_det d on(m.id=d.id_matricula) where m.saldo>0 and d.saldo>0 and d.item=(select min(item) from app_matriculas_det where id_matricula=m.id and saldo>0) and id_colegio=:colegioID and nro_dui LIKE :buscar";
      $qry = $db->query_all($sql,$params);
      if ($qry) {
        foreach($qry as $rs){
          $tabla[] = array(
            "ID" => $rs["id_matricula"],
            "nro_dui" => $rs["nro_dui"],
            "html_nro_DUI" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nro_dui"]),
            "alumnoID" => $rs["id_alumno"],
            "alumno" => $rs["alumno"],
            "codigo" => $rs["codigo"],
            "yyyy" => $rs["yyyy"],
            "nivel" => $rs["nivel"],
            "grado" => $rs["grado"],
            "seccion" => $rs["seccion"],
            "fecha_matricula" => $rs["fecha_matricula"],
            "productoID" => $rs["id_producto"],
            "saldo_det" => $rs["saldo_det"]*1,
            "atraso" => (($rs["atraso"]<0)?(0):($rs["atraso"]*1)),
            "importe" => $rs["importe"]*1,
            "saldo" => $rs["saldo"]*1,
            "rolUser" => $_SESSION['usr_data']['rolID'],
            "rolROOT" => 101
          );
        }
      }

      //respuesta
      $rpta = array("matriculas"=>$tabla);
      $db->enviarRespuesta($rpta);
      break;
    case "matricula_view":
      //respuesta
      $rpta = array(
        "comboTipoPago" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=13 order by id;"),
        "fecha" => $fn->getFechaActualDB());
      $db->enviarRespuesta($rpta);
      break;
    case "pago_ins":
      try{
        $userID = $_SESSION['usr_ID']; 
      
        $sql = "select fn_actualizar_saldos_generar_movimiento(:colegioID, :matriculaID, :cajeraID, :operID, :pagoID, :movID, :sysUSR, :sysIP, :importe, :observac) as id_movim";
        $params = [
          ":colegioID" => $web->colegioID,
          ":matriculaID" => $data->matriculaID,
          ":cajeraID" => $userID,
          ":operID" => 124, //credito
          ":pagoID" => $data->medioPagoID,
          ":movID" => 10, //tipo de movimiento
          ":importe" => $data->importe,
          ":sysIP" => $fn->getClientIP(),
          ":sysUSR" => $userID,
          ":observac" => ''
        ];
        $qry = $db->query_all($sql,$params);
        $rs = reset($qry);
        $movimID = $rs["id_movim"];

        //respuesta
        $rpta = array("error" => false, "movimID" => $movimID, "ingresados" => 1);
        $db->enviarRespuesta($rpta);
        break;
      } catch (PDOException $e) {
        $rpta = array("error" => true, "mensaje" => "Error al procesar el pago: ".$e->getMessage());
        $db->enviarRespuesta($rpta);
      }
  }
  $db->close();
?>
