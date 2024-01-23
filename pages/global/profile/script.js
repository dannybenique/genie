var rutaSQL = "pages/global/profile/sql.php";

//=========================funciones para profile============================
function appProfile(userID){
  let datos = {
    TipoQuery : 'viewPerfil',
    userID : userID
  }
  appFetch(datos,rutaSQL).then(resp => {
    // console.log(resp);
    let data = resp.tablaPers;
    let user = resp.user;
    document.querySelector("#div_PersAuditoria").style.display = ((user.rolID==user.rolROOT)?('block'):('none'));
    //info corta
    document.querySelector("#perfil_imagen").src = (user.urlfoto);
    document.querySelector("#perfil_nombrecorto").innerHTML = (user.nombrecorto);
    document.querySelector("#perfil_cargo").innerHTML = (user.cargo);
    document.querySelector("#perfil_DNI").innerHTML = (data.nroDUI);
    document.querySelector("#perfil_Celular").innerHTML = (data.celular);
    document.querySelector("#perfil_Agencia").innerHTML = (resp.agencia);
    document.querySelector("#perfil_Correo").innerHTML = (data.correo);
    document.querySelector("#perfil_Direccion").innerHTML = (data.direccion);

    //pestaña datos personales
    if(data.tipoPersona==2){ //persona juridica
      document.querySelector("#lbl_PersTipoNombres").innerHTML = ("Razon Social");
      document.querySelector("#lbl_PersTipoProfesion").innerHTML = ("Rubro");
      document.querySelector("#lbl_PersTipoApellidos").style.display = 'none';
      document.querySelector("#lbl_PersTipoSexo").style.display = 'none';
      document.querySelector("#lbl_PersTipoECivil").style.display = 'none';
      document.querySelector("#lbl_PersTipoGIntruc").style.display = 'none';
    }else{
      document.querySelector("#lbl_PersTipoNombres").innerHTML = ("Nombres");
      document.querySelector("#lbl_PersTipoProfesion").innerHTML = ("Profesion");
      document.querySelector("#lbl_PersTipoApellidos").style.display = 'block';
      document.querySelector("#lbl_PersTipoSexo").style.display = 'block';
      document.querySelector("#lbl_PersTipoECivil").style.display = 'block';
      document.querySelector("#lbl_PersTipoGIntruc").style.display = 'block';
    }
    document.querySelector("#lbl_PersNombres").innerHTML = (data.nombres);
    document.querySelector("#lbl_PersApellidos").innerHTML = (data.ap_paterno+" "+data.ap_materno);
    document.querySelector("#lbl_PersTipoDNI").innerHTML = (data.tipoDUI);
    document.querySelector("#lbl_PersNroDNI").innerHTML = (data.nroDUI);
    document.querySelector("#lbl_PersFechaNac").innerHTML = (moment(data.fechanac).format("DD/MM/YYYY"));
    document.querySelector("#lbl_PersEdad").innerHTML = (moment().diff(moment(data.fechanac),"years")+" años");
    document.querySelector("#lbl_PersPaisNac").innerHTML = (data.paisnac);
    document.querySelector("#lbl_PersLugarNac").innerHTML = (data.lugarnac);
    document.querySelector("#lbl_PersSexo").innerHTML = (data.sexo);
    document.querySelector("#lbl_PersEcivil").innerHTML = (data.ecivil);
    document.querySelector("#lbl_PersCelular").innerHTML = (data.celular);
    document.querySelector("#lbl_PersTelefijo").innerHTML = (data.telefijo);
    document.querySelector("#lbl_PersEmail").innerHTML = (data.correo);
    document.querySelector("#lbl_PersGInstruccion").innerHTML = (data.ginstruc);
    document.querySelector("#lbl_PersProfesion").innerHTML = (data.profesion);
    document.querySelector("#lbl_PersOcupacion").innerHTML = (data.ocupacion);
    document.querySelector("#lbl_PersUbicacion").innerHTML = (data.region+" - "+data.provincia+" - "+data.distrito);
    document.querySelector("#lbl_PersDireccion").innerHTML = (data.direccion);
    document.querySelector("#lbl_PersReferencia").innerHTML = (data.referencia);
    document.querySelector("#lbl_PersMedidorluz").innerHTML = (data.medidorluz);
    document.querySelector("#lbl_PersMedidorAgua").innerHTML = (data.medidoragua);
    document.querySelector("#lbl_PersTipovivienda").innerHTML = (data.tipovivienda);
    document.querySelector("#lbl_PersObservac").innerHTML = (data.observPers);
    document.querySelector("#lbl_PersSysFecha").innerHTML = (moment(data.sysfechaPers).format("DD/MM/YYYY HH:mm:ss"));
    document.querySelector("#lbl_PersSysUser").innerHTML = (data.sysuserPers);
  });
}

function appProfileCambiarPassw(userID,pass,repass){
  let miPass = document.querySelector(pass).value;
  let miRepass = document.querySelector(repass).value;

  if (miPass==miRepass){
    let datos = {
      TipoQuery : 'updPassword',
      pass : SHA1(miPass).toString().toUpperCase(),
      userID : userID
    }
    appFetch(datos,rutaSQL).then(resp => {
      if(!resp.error) { //sin errores
        document.querySelector(pass).value = "";
        document.querySelector(repass).value = "";
        alert("Cambio Hecho!!!");
      }
    });
  } else {
    alert("La clave no coincide");
  }
}
