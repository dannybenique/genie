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
        case "selMatriculas":
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
                "fecha" => $rs["fecha_matricula"],
                "nro_dui"=> str_replace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["nro_dui"]),
                "alumno" => str_ireplace($data->buscar, '<span style="background:yellow;">'.$data->buscar.'</span>', $rs["alumno"]),
                "nivel" => $rs["nivel"],
                "grado" => $rs["grado"],
                "seccion" => $rs["seccion"],
                "saldo" => $rs["saldo"]*1,
                "nro_cuotas" => $rs["nro_cuotas"]
              );
            }
          }

          //respuesta
          $rpta = array("tabla"=>$tabla,"cuenta"=>$rsCount["cuenta"]);
          echo json_encode($rpta);
          break;
        case "viewCredito": //visualiza el credito de la matricula
          //cabecera
          $prestamo = 0;
          $qry = $db->query_all("select * from vw_prestamos_ext where id=:id",[":id"=>$data->prestamoID]);
          if($qry) {
            $rs = reset($qry);
            
            $cabecera = array(
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
              "mon_abrevia" => $rs["mon_abrevia"],
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
              "estado" => $rs["estado"]*1
            );
          }

          //detalle
          $detalle = array();
          $qry = $db->query_all("select *,capital+interes+otros as total,case capital-pg_capital when 0 then atraso else extract(days from (now()-fecha)) end as atraso2,extract(days from now()-fecha)::float*(".$cabecera["mora"]."*0.01/360)*capital as mora from bn_prestamos_det where id_saldo=:id order by numero;",[":id"=>$data->prestamoID]);
          if ($qry) {
            foreach($qry as $rs){
              $detalle[] = array(
                "numero" => $rs["numero"]*1,
                "fecha" => $rs["fecha"],
                "total" => $rs["total"]*1,
                "capital" => $rs["capital"]*1,
                "interes" => $rs["interes"]*1,
                "mora" => ($rs["mora"]>=0)?($rs["mora"]*1):(0),
                "otros" => $rs["otros"]*1,
                "pg_capital" => $rs["pg_capital"]*1,
                "pg_interes" => $rs["pg_interes"]*1,
                "pg_mora" => $rs["pg_mora"]*1,
                "pg_otros" => $rs["pg_otros"]*1,
                "saldo" => $rs["saldo"]*1,
                "atraso" => $rs["atraso2"]*1
              );
            }
          }
          //respuesta
          $rpta = array('prestamo'=>$cabecera, "detalle"=>$detalle);
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
