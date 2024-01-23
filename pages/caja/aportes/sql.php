<?php
  include_once('../../../includes/sess_verifica.php');

  if(isset($_SESSION["usr_ID"])){
    if (isset($_REQUEST["appSQL"])){
      include_once('../../../includes/db_database.php');
      include_once('../../../includes/web_config.php');
      include_once('../../../includes/funciones.php');

      $data = json_decode($_REQUEST['appSQL']);
      $rpta = 0;

      //****************sql****************
      switch ($data->TipoQuery) {
        case "selAportes":
          $tabla = array();
          $buscar = strtoupper($data->buscar);
          $sql = "select * from vw_saldos where estado=1 and id_tipo_oper=121 and id_coopac=:coopacID and nro_dui LIKE :buscar";
          $params = [":coopacID"=>$web->coopacID,":buscar"=>'%'.$buscar.'%'];
          $qry = $db->query_all($sql,$params);
          if ($qry) {
            foreach($qry as $rs){
              $tabla[] = array(
                "ID" => $rs["id"],
                "nro_DUI" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nro_dui"]),
                "socio" => $rs["socio"],
                "codigo" => $rs["cod_prod"],
                "producto" => $rs["producto"],
                "mon_abrevia" => $rs["m_abrevia"],
                "saldo" => $rs["saldo"]*1
              );
            }
          }

          //respuesta
          $rpta = array("aportes"=>$tabla);
          echo json_encode($rpta);
          break;
        case "viewAporte":
          //saldos
          $qry = $db->query_all("select * from vw_saldos where id=".$data->saldoID);
          if($qry) {
            $rs = reset($qry);
            $aporte = array(
              "ID" => $rs["id"],
              "DUI" => $rs["dui"],
              "nro_dui" => $rs["nro_dui"],
              "socio" => $rs["socio"],
              "obliga"=> $rs["obliga"], //indica si es obligatorio=1, entonces debe tener un minimo de saldo
              "productoID" => $rs["id_producto"],
              "socioID" => $rs["id_socio"],
              "saldo" => $rs["saldo"]
            );
          }

          //respuesta
          $rpta = array(
            "aporte" => $aporte,
            "comboTipoPago" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=13 order by id;"),
            "comboMonedas" => $fn->getComboBox("select id,nombre from sis_tipos where id_padre=1 order by id;"),
            "fecha" => $fn->getFechaActualDB());
          echo json_encode($rpta);
          break;
        case "insOperacion":
          $tipo_operID = 121; //aportes
          $clientIP = $fn->getClientIP();
          $userID = $_SESSION['usr_ID'];

          /******agregamos bn_movim********/
          /********************************/
          $qry = $db->query_all("select coalesce(max(id)+1,1) as code from bn_movim;");
          $movimID = reset($qry)["code"];
          $qry = $db->query_all("select right('0000000'||cast(coalesce(max(right(codigo,7)::integer)+1,1) as text),7) as code from bn_movim where id_cajera=".$userID);
          $codigo = $userID."-".reset($qry)["code"];
          $sql = "insert into bn_movim values(:id,:coopacID,:agenciaID,:operID,:pagoID,:monedaID,:socioID,:saldoID,:productoID,:cajeraID,now(),:codigo,:importe,:estado,:sysIP,:userID,now(),:observac)";
          $params = [
            ":id"=>$movimID,
            ":coopacID"=>$web->coopacID,
            ":agenciaID"=>$data->agenciaID,
            ":operID"=>$tipo_operID,
            ":pagoID"=>$data->medioPagoID,
            ":monedaID"=>$data->monedaID,
            ":socioID"=>$data->socioID,
            ":saldoID"=>$data->saldoID,
            ":productoID"=>$data->productoID,
            ":cajeraID"=>$userID,
            ":codigo"=>$codigo,
            ":importe"=>$data->importe,
            ":estado"=>1,
            ":sysIP"=>$clientIP,
            ":userID"=>$userID,
            ":observac"=>''
          ];
          $qry = $db->query_all($sql,$params);
          $rs = reset($qry);

          //agregamos bn_movim_det
          $qry = $db->query_all("select coalesce(max(id)+1,1) as code from bn_movim_det;");
          $detalleID = reset($qry)["code"];
          $sql = "insert into bn_movim_det values(:id,:cabeceraID,:tipomovID,:item,:importe);";
          $params = [
            ":id"=>$detalleID,
            ":cabeceraID"=>$movimID,
            ":tipomovID"=>($data->tipoOperAporte==1)?(3):(4), //aporte o retiro
            ":item"=>1, //primero y unico
            ":importe"=>$data->importe
          ];
          $qry = $db->query_all($sql,$params);
          $rs = reset($qry);

          /******agregamos bn_saldos*******/
          /********************************/
          $sql = "update bn_saldos set saldo=(saldo ".(($data->tipoOperAporte==1)?("+"):("-"))." :importe),sys_ip=:sysIP,sys_user=:userID,sys_fecha=now() where id=:saldoID;";
          $params = [
            ":saldoID"=>$data->saldoID,
            ":importe"=>$data->importe,
            ":sysIP"=>$clientIP,
            ":userID"=>$userID
          ];
          $qry = $db->query_all($sql,$params);
          $rs = reset($qry);

          $rpta = array("error" => false,"movimID"=>$movimID,"ingresados" => 1);
          echo json_encode($rpta);
          break;
      }
      $db->close();
    } else{
      $resp = array("error"=>true,"mensaje"=>"ninguna variable en POST");
      echo json_encode($resp);
    }
  } else {
    $resp = array("error"=>true,"mensaje"=>"Caducó la sesion.");
    echo json_encode($resp);
  }
?>
