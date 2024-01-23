<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      include_once('../../../includes/web_config.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      //****************prestamos****************
      switch ($data->TipoQuery) {
        case "selAportes":
          //producto tipo aporte
          $params = [":coopacID"=>$web->coopacID,":operID"=>121];
          $qry = $db->query_all("select obliga from bn_productos where id_coopac=:coopacID and id_tipo_oper=:operID;",$params);
          $rsapo = ($qry) ? reset($qry) : null;
          
          //cuenta de saldos
          $tabla = array();
          $buscar = strtoupper($data->buscar);
          $whr = " and id_tipo_oper=121 and id_coopac=:coopacID and (socio LIKE :buscar or nro_dui LIKE :buscar) ";
          $params = [":coopacID"=>$web->coopacID,"buscar"=>'%'.$buscar.'%'];
          $qry = $db->query_all("select count(*) as cuenta from vw_saldos where estado=1 ".$whr.";",$params);
          $rsCount = reset($qry);
          //tabla de saldos por aporte
          $qry = $db->query_all("select * from vw_saldos where estado=1 ".$whr." limit 25 offset 0;",$params);
          if ($qry) {
            foreach($qry as $rs){
              $paramx = [":coopacID"=>$web->coopacID,":operID"=>$rs["id_tipo_oper"],":socioID"=>$rs["id_socio"]];
              $qrx = $db->query_all("select count(*) as cuenta from bn_movim where id_coopac=:coopacID and id_tipo_oper=:operID and id_socio=:socioID;",$paramx);
              $num_movim = reset($qrx)["cuenta"];
              $tabla[] = array(
                "ID" => $rs["id"],
                "nro_dui"=> str_replace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nro_dui"]),
                "socio" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["socio"]),
                "producto" => $rs["producto"],
                "codigo" => $rs["cod_prod"],
                "moneda" => $rs["moneda"],
                "m_abrevia" => $rs["m_abrevia"],
                "saldo" => $rs["saldo"]*1,
                "num_movim" => $num_movim*1
              );
            }
          }

          //respuesta
          $rpta = array(
            "tabla" => $tabla,
            "cuenta" => $rsCount["cuenta"],
            "obliga" => $rsapo["obliga"]*1 //indica si el aporte esta config en obligatorio
          );
          echo json_encode($rpta);
          break;
        case "insAportes":
          $tipo_oper_ID = 121;
          $qry = $db->query_all("select coalesce(max(id)+1,1) as code from bn_saldos;");
          $id = reset($qry)["code"];
          $qry = $db->query_all("select id from bn_productos where id_coopac=".$web->coopacID." and id_tipo_oper=".$tipo_oper_ID);
          $productoID = reset($qry)["id"];
          $qry = $db->query_all("select concat(to_char(now(),'YYYYMMDD'),'-',right('000000'||cast(coalesce(max(right(codigo,4)::integer)+1,1) as text),4)) as code from bn_saldos where left(codigo,8)=to_char(now(),'YYYYMMDD') and id_coopac=".$web->coopacID);
          $codigo = reset($qry)["code"];
          $sql = "insert into bn_saldos values(:id,:coopacID,:socioID,:operID,:productoID,:monedaID,:codigo,:saldo,:estado,:sysIP,:userID,now());";
          $params = [
            ":id"=>$id,
            ":coopacID"=>$web->coopacID,
            ":socioID"=>$data->socioID,
            ":operID"=>$tipo_oper_ID,
            ":productoID"=>$productoID,
            ":monedaID"=>111, //soles
            ":codigo"=>$codigo,
            ":saldo"=>0,
            ":estado"=>1,
            ":sysIP"=>$fn->getClientIP(), 
            ":userID"=>$_SESSION['usr_ID']
          ];
          $qry = $db->query_all($sql,$params);
          $rs = ($qry) ? (reset($qry)) : (null);

          //respuesta
          $rpta = array("error"=>false, "insert"=>1);
          echo json_encode($rpta);
          break;
        case "viewAporte":
          //cabecera
          $aporte = 0;
          $socioID = 0;
          $qry = $db->query_all("select * from vw_saldos where id=".$data->aporteID);
          if ($qry) {
            $rs = reset($qry);
            
            $socioID = $rs["id_socio"];
            $aporte = array(
              "ID" => $rs["id"],
              "socioID" => $rs["id_socio"],
              "socio" => $rs["socio"],
              "dui" => $rs["dui"],
              "nro_dui" => $rs["nro_dui"],
              "producto" => $rs["producto"],
              "cod_prod" => $rs["cod_prod"],
              "moneda" => $rs["moneda"],
              "saldo" => $rs["saldo"]*1,
              "estado" => ($rs["estado"]*1),
            );
          }

          //movimientos
          $movim = array();
          $params =[":coopacID"=>$web->coopacID,":operID"=>121,":socioID"=>$socioID];
          $qry = $db->query_all("select * from vw_movim where id_coopac=:coopacID and id_tipo_oper=:operID and id_socio=:socioID order by fecha;",$params);
          if ($qry) {
            foreach($qry as $rs){
              $movim[] = array(
                "ag" => $rs["codagenc"],
                "us" => $rs["coduser"],
                "fecha" => $rs["fecha"],
                "codigo" => $rs["codigo"],
                "codmov" => $rs["codmov"],
                "movim" => $rs["movim"],
                "ingresos" => ($rs["in_out"]==1 && $rs["afec_prod"]==1)?($rs["importe_det"]*1):(0),
                "salidas" => ($rs["in_out"]==0 && $rs["afec_prod"]==1)?($rs["importe_det"]*1):(0),
                "otros" => ($rs["afec_prod"]==0)?($rs["importe_det"]*1):(0)
              );
            }
          }
          //respuesta
          $rpta = array('aporte'=>$aporte, "movim"=>$movim);
          echo json_encode($rpta);
          break;
        case "VerifyAportes":
          $tablaPers = ""; //almacena los datos de la persona
          $persona = false; //indica que existe en personas
          $activo = false; //indica que encontro en tabla de socios
          
          //verificar en Personas
          $qry = $db->query_all("select id from personas where (nro_dui=:nrodni);",[":nrodni"=>$data->nroDNI]);
          if($qry){
            $rs = reset($qry);
            $tablaPers = $fn->getViewPersona($rs["id"]);
            $persona = true;
            //verificar en Aportes
            $qry = $db->query_all("select * from bn_socios where id_coopac=:coopacID and id_socio=:socioID;",[":coopacID"=>$web->coopacID,":socioID"=>$rs["id"]]);
            if($qry){
              $paramsAportes = [":coopacID"=>$web->coopacID,":socioID"=>$rs["id"]];
              $qryAportes = $db->query_all("select * from bn_saldos where id_tipo_oper=121 and id_coopac=:coopacID and id_socio=:socioID;",$paramsAportes);
              $activo = ($qryAportes) ? true : false;
            } else {
              $activo = 2;
            }
          }

          //respuesta
          $rpta = array(
            "tablaPers" => $tablaPers,
            "persona" => $persona,
            "activo" => $activo,
            "mensajeNOadd" => "ya tiene APORTES...");
          echo json_encode($rpta);
          break;
      }
      $db->close();
    } else {
      $resp = array("error"=>true,"data"=>$tabla,"mensaje"=>"ninguna variable en POST");
      echo json_encode($resp);
    }
  } else {
    $resp = array("error"=>true,"mensaje"=>"Caducó la sesion.");
    echo json_encode($resp);
  }
?>
