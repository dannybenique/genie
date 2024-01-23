<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/funciones.php');
      include_once('../../../includes/web_config.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      //****************personas****************
      function getViewSocio($personaID){
        $db = $GLOBALS["db"]; //base de datos
        $fn = $GLOBALS["fn"]; //funciones
        $web = $GLOBALS["web"]; //web-config
        
        //obtener datos personales
        $sql = "select s.*,b.nombre as agencia from bn_socios s join bn_bancos b on (s.id_agencia=b.id) where s.estado=1 and s.id_socio=:socioID and s.id_coopac=:coopacID";
        $params = [":socioID"=>$personaID,":coopacID"=>$web->coopacID];
        $qry = $db->query_all($sql,$params);
        
        if ($qry) {
            $rs = reset($qry);
            $tabla = array(
              "ID" => ($rs["id_socio"]),
              "coopacID" => ($rs["id_coopac"]),
              "agenciaID" => ($rs["id_agencia"]),
              "comboAgencias" => ($fn->getComboBox("select id,nombre from bn_bancos where estado=1 and id_padre=".$web->coopacID." order by codigo;")),
              "agencia" => $rs["agencia"],
              "fecha" => $rs["fecha"],
              "codigo" => $rs["codigo"],
              "observac" => ($rs["observac"]),
              "sysuser" => ($rs["sys_user"]),
              "sysfecha" => ($rs["sys_fecha"])
            );
        }
        return $tabla; 
      }
      switch ($data->TipoQuery) {
        case "selDesembolsos":
          $whr = "";
          $tabla = array();
          $buscar = strtoupper($data->buscar);
          $whr = " and id_coopac=:coopacID and (socio LIKE :buscar or nro_dui LIKE :buscar) ";
          $params = [":coopacID"=>$web->coopacID,":buscar"=>'%'.$buscar.'%'];
          $qry = $db->query_all("select count(*) as cuenta from vw_prestamos_min where estado=2 ".$whr.";",$params);
          $rsCount = reset($qry);

          $qry = $db->query_all("select * from vw_prestamos_min where estado=2 ".$whr." order by socio limit 25 offset 0;",$params);
          if ($qry) {
            foreach($qry as $rs){
              $tabla[] = array(
                "ID" => $rs["id"],
                "codigo" => $rs["codigo"],
                "fecha" => $rs["fecha_solicred"],
                "otorga" => $rs["fecha_otorga"],
                "nro_dui"=> str_replace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nro_dui"]),
                "socio" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["socio"]),
                "tipo_oper" => $rs["tipo_oper"],
                "producto" => $rs["producto"],
                "mon_abrevia" => $rs["mon_abrevia"],
                "tiposbs" => $rs["tipo_sbs"],
                "destsbs" => $rs["dest_sbs"],
                "tasa" => $rs["tasa"]*1,
                "importe" => $rs["importe"]*1,
                "nro_cuotas" => $rs["nro_cuotas"]*1
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount["cuenta"]);
          echo json_encode($rpta);
          break;
        
        case "viewDesembolso":
          $tabla = 0;
          $socioID = 0;
          $qry = $db->query_all("select *,now() as fecha_desemb from vw_prestamos_ext where id=".$data->SoliCredID);
          if ($qry) {
            $rs = reset($qry);
            $socioID = $rs["id_socio"];
            $date = new DateTime($rs["fecha_pricuota"]);
            $pivot = ($rs["id_tipocred"]==1)?($date->format('Ymd')):($rs["frecuencia"]);
            $cuota = $fn->getSimulacionCredito($rs["id_tipocred"],$rs["importe"],$rs["tasa_cred"],$rs["tasa_desgr"],$rs["nro_cuotas"],$rs["fecha_otorga"],$pivot);
            $tabla = array(
              "ID" => $rs["id"],
              "codigo" => $rs["codigo"],
              "socio" => $rs["socio"],
              "coopacID" => $rs["id_coopac"],
              "agenciaID" => $rs["id_agencia"],
              "tipocredID" => $rs["id_tipocred"],
              "productoID" => $rs["id_producto"],
              "socioID" => $rs["id_socio"],
              "monedaID" => $rs["id_moneda"],
              "agencia" => $rs["agencia"],
              "promotor" => $rs["promotor"],
              "analista" => $rs["analista"],
              "aprueba" => ($rs["id_aprueba"]==null)?(""):($rs["aprueba"]),
              "producto" => $rs["producto"],
              "tiposbs" => $rs["tiposbs"],
              "destsbs" => $rs["destsbs"],
              "clasifica" => $rs["clasifica"],
              "condicion" => $rs["condicion"],
              "moneda" => $rs["moneda"],
              "mon_abrevia" => $rs["mon_abrevia"],
              "importe" => $rs["importe"],
              "saldo" => $rs["saldo"],
              "tasa_cred" => $rs["tasa_cred"],
              "tasa_mora" => $rs["tasa_mora"],
              "tasa_desgr" => $rs["tasa_desgr"],
              "nrocuotas" => $rs["nro_cuotas"],
              "fecha_desemb" => $rs["fecha_desemb"],
              "fecha_solicred" => $rs["fecha_solicred"],
              "fecha_otorga" => $rs["fecha_otorga"],
              "fecha_pricuota" => $rs["fecha_pricuota"],
              "tipocred" => $rs["tipocred"],
              "frecuencia" => $rs["frecuencia"],
              "cuota" => $cuota[1]["cuota"],
              "observac" => $rs["observac"],
              "estado" => ($rs["estado"]*1),
              "rolUser" => $_SESSION['usr_data']['rolID'],
              "rolROOT" => 101
            );
          }
          
          //respuesta
          $rpta = array('tablaDesembolso'=> $tabla,'tablaPers'=>$fn->getViewPersona($socioID));
          echo json_encode($rpta);
          break;
        case "delDesembolsos":
          $userID = $_SESSION['usr_ID'];
          $clientIP = $fn->getClientIP();

          for($i=0; $i<count($data->arr); $i++){
            $id = $data->arr[$i];
            $params = [":id"=>$id,":sysIP"=>$clientIP,":userID"=>$userID];
            $qry = $db->query_all("update bn_saldos set estado=3,sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:id",$params);
            $qry = $db->query_all("update bn_prestamos set id_aprueba=null,fecha_aprueba=null where id_saldo=".$id);
            $rs = reset($qry);
          }
          //respuesta
          $rpta = array("error"=>false, "delete"=>$data->arr);
          echo json_encode($rpta);
          break;
        case "ejecutarDesembolso":
          $userID = $_SESSION['usr_ID'];
          $clientIP = $fn->getClientIP();

          $sql = "select sp_desembolsos ('DESEM',:saldoID,:socioID,:coopacID,:agenciaID,:productoID,:monedaID,:tipopagoID,:importe,:tasa,:desgr,:nrocuotas,:fechaOtor,:tipocredID,:pivot,:sysIP,:userID) as movim_id;";
          $params = [
            ":saldoID"=>$data->ID,
            ":socioID"=>$data->socioID,
            ":coopacID"=>$web->coopacID,
            ":agenciaID"=>$data->agenciaID,
            ":productoID"=>$data->productoID,
            ":monedaID"=>$data->monedaID,
            ":tipopagoID"=>$data->tipopagoID,
            ":importe"=>$data->importe,
            ":tasa"=>$data->tasa_cred,
            ":desgr"=>$data->tasa_desgr,
            ":nrocuotas"=>$data->nrocuotas,
            ":fechaOtor"=>$data->fecha_otorga,
            ":tipocredID"=>$data->tipocredID,
            ":pivot"=>$data->pivot,
            ":sysIP"=>$clientIP,
            ":userID"=>$userID
          ];
          
          $qry = $db->query_all($sql,$params);
          if($qry){
            $rs = reset($qry);
            $rpta = array("error"=>false, "insert"=>1, "movimID"=>$rs["movim_id"]);
          } else {
            $rpta = array("error"=>true, "insert"=>0);
          }
          
          //respuesta
          echo json_encode($rpta);
          break;
      }
      $db->close();
    } else{
      $resp = array("error"=>true,"data"=>$tabla,"mensaje"=>"ninguna variable en POST");
      echo json_encode($resp);
    }
  } else {
    $resp = array("error"=>true,"mensaje"=>"Caducó la sesion.");
    echo json_encode($resp);
  }
?>
