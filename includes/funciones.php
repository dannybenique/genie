<?php
  class funciones{
    //Obtiene la IP del cliente
    public function getClientIP() {
      $ipaddress = '';
      if (getenv('HTTP_CLIENT_IP')) $ipaddress = getenv('HTTP_CLIENT_IP');
      else if(getenv('HTTP_X_FORWARDED_FOR')) $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
      else if(getenv('HTTP_X_FORWARDED')) $ipaddress = getenv('HTTP_X_FORWARDED');
      else if(getenv('HTTP_FORWARDED_FOR')) $ipaddress = getenv('HTTP_FORWARDED_FOR');
      else if(getenv('HTTP_FORWARDED')) $ipaddress = getenv('HTTP_FORWARDED');
      else if(getenv('REMOTE_ADDR')) $ipaddress = getenv('REMOTE_ADDR');
      else $ipaddress = 'UNKNOWN';
      return $ipaddress; 
    }
    public function getValorCampo($cadSQL,$campo){ //devuelve el valor de UN SOLO campo segun la consulta
      $db = $GLOBALS["db"];
      $qry = $db->query_all($cadSQL);
      $rs = reset($qry);
      return $rs[$campo];
    }
    public function getComboBox($cadSQL) { //devuelve una lista clave valor para llenarla en un combobox segun la consulta
      $db = $GLOBALS["db"];
      $tabla = array();
      $qry = $db->query_all($cadSQL);
      if ($qry) {
        foreach($qry as $rs){
          $tabla[] = array(
            "ID" => $rs["id"],
            "nombre" => $rs["nombre"]
          );
        }
      }
      return $tabla; 
    }
    public function getFechaActualDB(){
      $db = $GLOBALS["db"];
      //obtener fecha actual de operacion
      $qry = $db->query_all("select cast(now() as date) as fecha");
      $rs = reset($qry);
      return $rs["fecha"];
    }
    public function getConfigColegio($colegioID){
      $db = $GLOBALS["db"];
      //obtener fecha actual de operacion
      $qry = $db->query_all("select config from app_colegios where id=".$colegioID);
      $rs = reset($qry);
      return $rs["config"];
    }
    
    //funciones para persona
    public function getAllPersonas($buscar,$pos){
      $db = $GLOBALS["db"];
      //verificar usuario
      //$qryusr = $db->query("select id_usernivel from tb_usuarios where id_persona=".$_SESSION['usr_ID'].";");
      //$rsusr = $db->fetch_array($qryusr);

      //cargar datos de Personas
      $tabla = array();
      $buscar = strtoupper($buscar);
      $whr = " and (persona LIKE :buscar or nro_dui LIKE :buscar) ";
      $params = [":buscar"=>'%'.$buscar.'%'];
      $sql = "select count(*) as cuenta from vw_personas where id>1 ".$whr.";";
      $qryCount = $db->query_all($sql,$params);
      $rsCount = ($qryCount) ? reset($qryCount)["cuenta"] : (0);

      $sql = "select * from vw_personas where id>1 ".$whr." order by persona limit 25 offset $pos;";
      $qry = $db->query_all($sql,$params);
      if ($qry) {
        foreach($qry as $rs){
          $tabla[] = array(
            "ID" => $rs["id"],
            "DNI"=> str_replace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["nro_dui"]),
            "persona" => str_ireplace($buscar, '<span style="background:yellow;">'.$buscar.'</span>', $rs["persona"]),
            "url" => $rs["urlfoto"],
            "direccion" => ($rs["direccion"])
          );
        }
      }
      return array("cuenta"=>$rsCount,"tabla"=>$tabla);
    }
    public function getEditPersona($personaID) {
      $db = $GLOBALS["db"];
      $fn = $GLOBALS["fn"];
      //verificar usuario
      //$qry = $db->query(utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";"));
      //$rs = $db->fetch_array($qry);
      //$tablaUser = array("usernivel"=>$rs["id_usernivel"],"admin"=>701);
  
      //verificar permisos
      //$sql = "select * from dbo.tb_usuarios_permisos where tabla='tb_personas' and id_usuario_solic=".$_SESSION['usr_ID']." and id_persona=".$personaID;
      //$qry = $db->select($sql);
      //if($db->has_rows($qry)){ $rs = $db->fetch_array($qry); $permisoPersona = array("ID"=>$rs["ID"],"estado"=>(1 + $rs["estado"])); }
      //else { $permisoPersona = array("ID"=>0,"estado"=>0); }
  
      //obtener datos personales
      $sql = "select p.*,id_distrito,id_provincia,id_region from personas p,vw_ubigeo u where p.id_ubigeo=u.id_distrito and p.id=:id";
      $params = [":id"=>$personaID];
      $qry = $db->query_all($sql,$params);
      
      if ($qry) {
          $rs = reset($qry);
          $tabla = array(
            "ID" => ($rs["id"]),
            "tipoPersona" => ($rs["tipo_persona"]),
            "urlfoto" => ($rs["urlfoto"]),
            "nombres" => ($rs["nombres"]),
            "ap_paterno" => ($rs["ap_paterno"]),
            "ap_materno" => ($rs["ap_materno"]),
            "comboDUI" => ($fn->getComboBox("select id,nombre from personas_aux where id_padre=5 order by orden;")),
            "id_dui" => $rs["id_dui"],
            "nroDUI" => ($rs["nro_dui"]),
            "celular" => ($rs["celular"]),
            "telefijo" => ($rs["telefijo"]),
            "correo" => ($rs["email"]),
            "profesion" => ($rs["profesion"]),
            "ocupacion" => ($rs["ocupacion"]),
            "fechanac" => ($rs["fecha_nac"]),
            "lugarnac" => ($rs["lugar_nac"]),
            "comboPais" => ($fn->getComboBox("select id,nombre from sis_ubigeo where tipo=1 order by nombre;")),
            "comboSexo" => ($fn->getComboBox("select id,nombre from personas_aux where id_padre=1 order by orden;")),
            "comboECivil" => ($fn->getComboBox("select id,nombre from personas_aux where id_padre=2 order by orden;")),
            "comboGInstruc" => ($fn->getComboBox("select id,nombre from personas_aux where id_padre=3 order by orden;")),
            "comboTipoViv" => ($fn->getComboBox("select id,nombre from personas_aux where id_padre=4 order by orden;")),
            "comboRegiones" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=101 order by nombre;")),
            "comboProvincias" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=".$rs["id_region"]." order by nombre;")),
            "comboDistritos" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=".$rs["id_provincia"]." order by nombre;")),
            "id_paisnac" => ($rs["id_paisnac"]),
            "id_sexo" => ($rs["id_sexo"]),
            "id_ecivil" => ($rs["id_ecivil"]),
            "id_ginstruc" => ($rs["id_ginstruccion"]),
            "id_tipovivienda" => $rs["id_tipovivienda"],
            "id_distrito" => ($rs["id_distrito"]),
            "id_provincia" => ($rs["id_provincia"]),
            "id_region" => ($rs["id_region"]),
            "direccion" => ($rs["direccion"]),
            "referencia" => ($rs["referencia"]),
            "medidorluz" => ($rs["medidorluz"]),
            "medidoragua" => ($rs["medidoragua"]),
            "observPers" => ($rs["observac"]),
            "sysuserPers" => ($rs["sys_user"]),
            "sysfechaPers" => ($rs["sys_fecha"])
            //"permisoPersona"=>$permisoPersona,
            //"tablaUser" => $tablaUser
          );
      }
      return $tabla; 
    }
    public function getViewPersona($personaID) {
      $db = $GLOBALS["db"];
      //verificar usuario
      //$qry = $db->query(utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";"));
      //$rs = $db->fetch_array($qry);
      //$tablaUser = array("usernivel"=>$rs["id_usernivel"],"admin"=>701);
  
      //verificar permisos
      //$sql = "select * from dbo.tb_usuarios_permisos where tabla='tb_personas' and id_usuario_solic=".$_SESSION['usr_ID']." and id_persona=".$personaID;
      //$qry = $db->select($sql);
      //if($db->has_rows($qry)){ $rs = $db->fetch_array($qry); $permisoPersona = array("ID"=>$rs["ID"],"estado"=>(1 + $rs["estado"])); }
      //else { $permisoPersona = array("ID"=>0,"estado"=>0); }
  
      //obtener datos personales
      $sql = "select p.*,fn_get_persona(p.tipo_persona,p.ap_paterno,p.ap_materno,p.nombres) AS persona,du.nombre as dui,gi.nombre as ginstruc,ec.nombre as ecivil,sx.nombre as sexo,tv.nombre as tipovivienda,be.nombrecorto,ps.nombre as pais_nac,u.id_region,u.region,u.id_provincia,u.provincia,u.id_distrito,u.distrito from personas p join personas_aux du on p.id_dui=du.id join personas_aux gi on p.id_ginstruccion=gi.id join personas_aux ec on p.id_ecivil=ec.id join personas_aux sx on p.id_sexo=sx.id join personas_aux tv on p.id_tipovivienda=tv.id join app_empleados be on p.sys_user=be.id join sis_ubigeo ps on p.id_paisnac=ps.id join vw_ubigeo u on p.id_ubigeo=u.id_distrito where p.id=:id";
      $params = [':id'=>$personaID];
      $qry = $db->query_all($sql,$params);
      
      if ($qry) {
        $rs = reset($qry);
        $tabla = array(
          "ID" => ($rs["id"]),
          "tipoPersona" => ($rs["tipo_persona"]),
          "persona" => ($rs["persona"]),
          "urlfoto" => ($rs["urlfoto"]),
          "nombres" => ($rs["nombres"]),
          "ap_paterno" => ($rs["ap_paterno"]),
          "ap_materno" => ($rs["ap_materno"]),
          "id_dui" => $rs["id_dui"],
          "tipoDUI" => $rs["dui"],
          "nroDUI" => ($rs["nro_dui"]),
          "celular" => ($rs["celular"]),
          "telefijo" => ($rs["telefijo"]),
          "correo" => ($rs["email"]),
          "profesion" => ($rs["profesion"]),
          "ocupacion" => ($rs["ocupacion"]),
          "fechanac" => ($rs["fecha_nac"]),
          "lugarnac" => ($rs["lugar_nac"]),
          "paisnac" => ($rs["pais_nac"]),
          "id_ginstruc" => ($rs["id_ginstruccion"]),
          "ginstruc" => ($rs["ginstruc"]),
          "id_ecivil" => ($rs["id_ecivil"]),
          "ecivil" => ($rs["ecivil"]),
          "id_sexo" => ($rs["id_sexo"]),
          "sexo" => ($rs["sexo"]),
          "id_region" => ($rs["id_region"]),
          "region" => ($rs["region"]),
          "id_provincia" => ($rs["id_provincia"]),
          "provincia" => ($rs["provincia"]),
          "id_distrito" => ($rs["id_distrito"]),
          "distrito" => ($rs["distrito"]),
          "direccion" => ($rs["direccion"]),
          "referencia" => ($rs["referencia"]),
          "medidorluz" => ($rs["medidorluz"]),
          "medidoragua" => ($rs["medidoragua"]),
          "id_tipovivienda" => $rs["id_tipovivienda"],
          "tipovivienda" => $rs["tipovivienda"],
          "observPers" => ($rs["observac"]),
          "sysuserPers" => ($rs["nombrecorto"]),
          "sysfechaPers" => ($rs["sys_fecha"])
          //"permisoPersona"=>$permisoPersona,
          //"tablaUser" => $tablaUser
        );
      }
      return $tabla; 
    }
    public function getAllLaborales($personaID){
      $db = $GLOBALS["db"];
      //todos los laborales
      $tablaLabo = array();
      $sql = "select * from personas_labo where estado=1 and id_persona=".$personaID." order by empresa;";
      $qry = $db->query_all($sql);
      if ($qry) {
        foreach($qry as $rs){
          $tablaLabo[] = array(
            "ID" => $rs["id"],
            "condicion" => $rs["condicion"],
            "empresa" => $rs["empresa"],
            "ruc" => $rs["ruc"],
            "cargo" => $rs["cargo"],
            "ingreso" => $rs["ingreso"]
          );
        }
      }
      return $tablaLabo;
    }
    public function getEditLaboral($laboralID) {
      $db = $GLOBALS["db"];
      $fn = $GLOBALS["fn"];

      //obtener datos laborales
      $sql = "select p.*,id_distrito,id_provincia,id_region from personas_labo p,vw_ubigeo u where p.id_ubigeo=u.id_distrito and p.id=:id";
      $params = [":id"=>$laboralID];
      $qry = $db->query_all($sql,$params);
  
      if ($qry) {
        $rs = reset($qry);
        $tabla = array(
          "ID" => ($rs["id"]),
          "id_persona" => ($rs["id_persona"]),
          "condicion" => ($rs["condicion"]),
          "empresa" => ($rs["empresa"]),
          "ruc" => ($rs["ruc"]),
          "telefono" => ($rs["telefono"]),
          "rubro" => ($rs["rubro"]),
          "id_region" => ($rs["id_region"]),
          "id_provincia" => ($rs["id_provincia"]),
          "id_distrito" => ($rs["id_distrito"]),
          "comboRegiones" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=101 order by nombre;")),
          "comboProvincias" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=".$rs["id_region"]." order by nombre;")),
          "comboDistritos" => ($fn->getComboBox("select id,nombre from sis_ubigeo where id_padre=".$rs["id_provincia"]." order by nombre;")),
          "direccion" => ($rs["direccion"]),
          "cargo" => ($rs["cargo"]),
          "ingreso" => ($rs["ingreso"]),
          "fechaIni" => ($rs["fecha_ini"]),
          "observLabo" => ($rs["observac"]),
          "sysuserLabo" => ($rs["sys_user"]),
          "sysfechaLabo" => ($rs["sys_fecha"])
          //"permisoPersona"=>$permisoPersona,
          //"tablaUser" => $tablaUser
        );
      }
      return $tabla; 
    }
    public function getViewLaboral($laboralID){
      $db = $GLOBALS["db"];

      //verificar usuario
      /*
      $qry = $db->query("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";");
      $rs = $db->fetch_array($qry);
      $tablaUser = array("usernivel"=>$rs["id_usernivel"],"admin"=>701);

      //verificar permisos
      $qry = $db->query("select * from dbo.tb_usuarios_permisos where tabla='tb_personas_labo' and id_usuario_solic=".$_SESSION['usr_ID']." and id_persona=".$personaID);
      if($db->has_rows($qry)){ $rs = $db->fetch_array($qry); $permisoLaboral = array("ID"=>$rs["ID"],"estado"=>(1 + $rs["estado"])); }
      else { $permisoLaboral = array("ID"=>0,"estado"=>0); }
      */

      //cargar datos laborales
      $sql = "select p.*,be.nombrecorto,u.id_region,u.region,u.id_provincia,u.provincia,u.id_distrito,u.distrito from personas_labo p,app_empleados be,vw_ubigeo u where p.sys_user=be.id_empleado and u.id_distrito=p.id_ubigeo and p.id=:id";
      $params = [":id"=>$laboralID];
      $qry = $db->query_all($sql,$params);
      if ($qry) {
        $rs = reset($qry);
        $tabla = array(
          "id_persona" => ($rs["id_persona"]),
          "condicion" => ($rs["condicion"]),
          "empresa" => ($rs["empresa"]),
          "ruc" => ($rs["ruc"]),
          "telefono" => ($rs["telefono"]),
          "rubro" => ($rs["rubro"]),
          "region" => ($rs["region"]),
          "provincia" => ($rs["provincia"]),
          "distrito" => ($rs["distrito"]),
          "direccion" => ($rs["direccion"]),
          "cargo" => ($rs["cargo"]),
          "ingreso" => ($rs["ingreso"]),
          "fechaIni" => ($rs["fecha_ini"]),
          "observLabo" => ($rs["observac"]),
          "sysuserLabo" => ($rs["nombrecorto"]),
          "sysfechaLabo" => ($rs["sys_fecha"])
          //"permisoLaboral" => $permisoLaboral,
          //"tablaUser" => $tablaUser
        );
      } else { $tabla = array("id_persona" => 0); }
      return $tabla; 
    }
    public function getViewConyuge($personaID) {
      $db = $GLOBALS["db"];
      $fn = $GLOBALS["fn"];

      //verificar usuario
      /*
      $qry = $db->select(utf8_decode("select id_usernivel from dbo.tb_usuarios where id_persona=".$_SESSION['usr_ID'].";"));
      $rs = $db->fetch_array($qry);
      $tablaUser = array("usernivel"=>$rs["id_usernivel"],"admin"=>701);
  
      //verificar permisos
      $qry = $db->select("select * from dbo.tb_usuarios_permisos where tabla='tb_personas_cony' and id_usuario_solic=".$_SESSION['usr_ID']." and id_persona=".$personaID);
      if($db->has_rows($qry)){ $rs = $db->fetch_array($qry); $permisoConyuge = array("ID"=>$rs["ID"],"estado"=>(1 + $rs["estado"])); }
      else { $permisoConyuge = array("ID"=>0,"estado"=>0); }
      */
  
      //verificar si la persona tiene conyuge
      $qry1 =  $db->query_all("select * from personas_rela where id_persona1=".$personaID);
      $qry2 =  $db->query_all("select * from personas_rela where id_persona2=".$personaID);
  
      if ($qry1 || $qry2) {
        if ($qry1) { $rs = reset($qry1); $conyugeID = $rs["id_persona2"]; }
        else { $rs = reset($qry2); $conyugeID = $rs["id_persona1"]; }
        $tabla = array(
          "id_conyuge" => ($conyugeID),
          "persona" => $fn->getViewPersona($conyugeID),
          "tiempoRelacion" => ($rs["tiemporelacion"])
          //"permisoConyuge" => $permisoConyuge,
          //"tablaUser" => $tablaUser
        );
      } else { $tabla = array("id_conyuge"=>0); }
      return $tabla; 
    }
    public function getSimulacionCredito($TipoCredito,$importe,$tasa_cred,$tasa_desgr,$nroCuotas,$fecha,$pivot) {
      $db = $GLOBALS["db"];
      //extraes data
      $tabla = array();
      $params = [
        ":importe"=>$importe,
        ":tasacred"=>$tasa_cred,
        ":tasadesgr"=>$tasa_desgr,
        ":nrocuotas"=>$nroCuotas,
        ":fecha"=>$fecha,
        ":pivot"=>$pivot
      ];

      switch($TipoCredito){
        case "1": $sql = "select * from fn_get_planpagos_fechafija(:importe,:tasacred,:tasadesgr,:nrocuotas,:fecha,:pivot) as (num integer,fecha date,dias integer,tasa_efec float,cuotax numeric,cuota numeric,capital numeric,interes numeric,desgr numeric,saldo numeric)"; break;
        case "2": $sql = "select * from fn_get_planpagos_plazofijo(:importe,:tasacred,:tasadesgr,:nrocuotas,:fecha,:pivot) as (num integer,fecha date,dias integer,tasa_efec float,cuotax numeric,cuota numeric,capital numeric,interes numeric,desgr numeric,saldo numeric)"; break;
      }
      
      $qry = $db->query_all($sql,$params);
      if ($qry) {
        foreach($qry as $rs){
          $tabla[] = array(
            "nro" => $rs["num"],
            "dias" => $rs["dias"],
            "fecha"=> $rs["fecha"],
            "cuota"=> $rs["cuota"],
            "capital" => $rs["capital"],
            "interes" => $rs["interes"],
            "desgr" => $rs["desgr"],
            "saldo" => $rs["saldo"]
          );
        }
      }
      return $tabla; 
    }
  }
  $fn = new funciones();
?>
