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
    case "insPago":
      $estado = 1;
      $tipo_operID = 124; //credito
      $colegioID = $web->colegioID;
      $clientIP = $fn->getClientIP();
      $userID = $_SESSION['usr_ID'];
      $importe = $data->importe;

      //actualizamos saldos en app_matriculas_det
      $pg_capital = 0;
      $pg_tot_capital = 0;
      $params = [":id"=>$data->matriculaID];
      $qry = $db->query_all("select id_matricula,item,vencimiento,saldo from app_matriculas_det where id_matricula=:id and saldo>0 order by item;",$params);
      if($qry) {
        foreach($qry as $rs){
          if($importe>0){
            $importe -= ($pg_capital = pago_Item(($rs["saldo"]),$importe));
            $pg_tot_capital += $pg_capital;
            $paramx = [":saldo"=>$pg_capital,":matriculaID"=>$data->matriculaID,":item"=>$rs["item"]];
            $qrx = $db->query_all("update app_matriculas_det set saldo=(saldo-:saldo) where id_matricula=:matriculaID and item=:item;","paramx");
            $xx = reset($qrx);
          } else { break; }
        }
      }
      
      //actualizamos saldo de app_matriculas
      $params = [":id"=>$data->matriculaID,":saldo"=>$data->importe,":sysIP"=>$clientIP,":userID"=>$userID];
      $qry = $db->query_all("update app_matriculas set saldo=(saldo-:saldo),sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id;",$params);
      $rs = reset($qry);


      /******agregamos app_movim********/
      /********************************/
      $qry = $db->query_all("select right('0000000'||cast(coalesce(max(right(codigo,7)::integer)+1,1) as text),7) as code from app_movim where id_cajera=".$userID);
      $codigo = $userID."-".reset($qry)["code"];
      $sql = "insert into app_movim(id_colegio,id_matricula,id_tipo_oper,id_tipo_pago,id_tipo_mov,id_cajera,fecha,codigo,total,estado,sys_ip,sys_user,sys_fecha,observac) values(:colegioID,:matriculaID,:operID,:pagoID,:movID,:cajeraID,now(),:codigo,:total,:estado,:sysIP,:sysUSER,now(),:observac) returning id;";
      $params = [
        ":colegioID"=>$colegioID,
        ":matriculaID"=>$data->matriculaID,
        ":operID"=>$tipo_operID,
        ":pagoID"=>$data->medioPagoID,
        ":movID"=>10, //tipo de movimiento
        ":cajeraID"=>$userID,
        ":codigo"=>$codigo,
        ":total"=>$data->importe,
        ":estado"=>$estado,
        ":sysIP"=>$clientIP,
        ":sysUSER"=>$userID,
        ":observac"=>''
      ];
      $qry = $db->query_all($sql,$params);
      $rs = reset($qry);
      $movimID = $rs["id"];

      //agregamos app_movim_det
      $sql = "insert into app_movim_det(id_movim,item,id_producto,importe) values(:movimID,:item,:productoID,:importe) returning id;";
      $params = [
        ":movimID"=>$movimID,
        ":item"=>1,
        ":productoID"=>$data->productoID,
        ":importe"=>$data->importe
      ];
      $qry = $db->query_all($sql,$params);
      $rs = reset($qry);

      //respuesta
      $rpta = array("error" => false,"movimID"=>$movimID,"ingresados" => 1);
      $db->enviarRespuesta($rpta);
      break;
  }
  $db->close();
?>
