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
        case "selCreditos":
          $whr = "";
          $tabla = array();
          $data->buscar = pg_escape_string(strtoupper($data->buscar));
          $whr = " and id_coopac=".$web->coopacID." and (socio like '%".$data->buscar."%' or nro_dui like '%".$data->buscar."%') ";
          $rsCount = $db->fetch_array($db->query("select count(*) as cuenta from vw_prestamos_min where estado=1 and saldo>0 ".$whr.";"));

          $qry = $db->query("select * from vw_prestamos_min where estado=1 and saldo>0 ".$whr." limit 25 offset 0;");
          if ($db->num_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $tabla[] = array(
                "ID" => $rs["id"],
                "codigo" => $rs["codigo"],
                "fecha" => $rs["fecha_otorga"],
                "nro_dui"=> str_replace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["nro_dui"]),
                "socio" => str_ireplace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["socio"]),
                "tipo_oper" => $rs["tipo_oper"],
                "producto" => $rs["producto"],
                "mon_abrevia" => $rs["mon_abrevia"],
                "tiposbs" => $rs["tipo_sbs"],
                "destsbs" => $rs["dest_sbs"],
                "tasa" => $rs["tasa"]*1,
                "importe" => $rs["importe"]*1,
                "saldo" => $rs["saldo"]*1,
                "nro_cuotas" => $rs["nro_cuotas"]*1
              );
            }
          }
          $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount["cuenta"]);
          echo json_encode($rpta);
          break;
        case "viewCredito":
          //cabecera
          $prestamo = 0;
          $qry = $db->query_params("select * from vw_prestamos_ext where id=$1",array($data->prestamoID));
          if ($db->num_rows($qry)) {
            $rs = $db->fetch_array($qry);
            
            $prestamo = array(
              "ID" => $rs["id"],
              "codigo" => $rs["codigo"],
              "socio" => $rs["socio"],
              "dui" => $rs["dui"],
              "nro_dui" => $rs["nro_dui"],
              "agencia" => $rs["agencia"],
              "promotor" => $rs["promotor"],
              "analista" => $rs["analista"],
              "producto" => $rs["producto"],
              "tiposbs" => $rs["tiposbs"],
              "destsbs" => $rs["destsbs"],
              "clasifica" => $rs["clasifica"],
              "condicion" => $rs["condicion"],
              "moneda" => $rs["moneda"],
              "importe" => $rs["importe"],
              "saldo" => $rs["saldo"],
              "tasa" => $rs["tasa_cred"],
              "mora" => $rs["tasa_mora"],
              "desgr" => $rs["tasa_desgr"],
              "nrocuotas" => $rs["nro_cuotas"],
              "fecha_solicred" => $rs["fecha_solicred"],
              "fecha_otorga" => $rs["fecha_otorga"],
              "fecha_pricuota" => $rs["fecha_pricuota"],
              "tipocred" => $rs["tipocred"],
              "frecuencia" => $rs["frecuencia"],
              "observac" => $rs["observac"],
              "estado" => ($rs["estado"]*1),
            );
          }

          //detalle
          $detalle = array();
          $qry = $db->query_params("select *,capital+interes+seguro+gastos as total from bn_prestamos_det where id_prestamo=$1",array($data->prestamoID));
          if ($db->num_rows($qry)) {
            for($xx = 0; $xx<$db->num_rows($qry); $xx++){
              $rs = $db->fetch_array($qry);
              $detalle[] = array(
                "numero" => $rs["numero"],
                "fecha" => $rs["fecha"],
                "total" => $rs["total"]*1,
                "capital" => $rs["capital"]*1,
                "interes" => $rs["interes"]*1,
                "seguro" => $rs["seguro"]*1,
                "gastos" => $rs["gastos"]*1,
                "pg_capital" => $rs["pg_capital"]*1,
                "pg_interes" => $rs["pg_interes"]*1,
                "pg_seguro" => $rs["pg_seguro"]*1,
                "pg_gastos" => $rs["pg_gastos"]*1,
                "saldo" => $rs["saldo"]*1
              );
            }
          }
          //respuesta
          $rpta = array('prestamo'=>$prestamo, "detalle"=>$detalle);
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
