//=========================funciones para crear el modal de laboral============================
(function (window,document){
  var iniLaboral = function(){
      var Laboral = {
        rutaSQL : "pages/modals/laboral/mod.sql.php",
        rutaHTML : "pages/modals/laboral/mod.laboral.htm",
        ID : 0,
        personaID : 0,
        commandSQL : "",
        tablaLabo : 0,
        addModalToParentForm : function(contenedor) { $("#"+contenedor).load(Laboral.rutaHTML); },
        close : function(){ $("#modalLabo").modal("hide"); },
        nuevo : async function(personaID){
          try {
            const resp = await appAsynFetch({TipoQuery:'newLaboral'}, Laboral.rutaSQL);
            Laboral.commandSQL = "INS";
            Laboral.ID = 0;
            Laboral.personaID = personaID;
            document.querySelector('#hid_modLaboPermisoID').value = ("");
            document.querySelector("#cbo_LaboCondicion").value = (0);
            document.querySelector("#txt_LaboEmpresa").value = ("");
            document.querySelector("#txt_LaboEmprRUC").value = ("");
            document.querySelector("#txt_LaboEmprFono").value = ("");
            document.querySelector("#txt_LaboEmprRubro").value = ("");
            document.querySelector("#txt_LaboEmprDireccion").value = ("");
            document.querySelector("#txt_LaboEmprCargo").value = ("");
            document.querySelector("#txt_LaboEmprIngreso").value = (appFormatMoney(0,2));
            document.querySelector("#txt_LaboObservac").value = ("");
            appLlenarDataEnComboBox(resp.comboRegiones,"#cbo_LaboEmprRegion",1014);
            appLlenarDataEnComboBox(resp.comboProvincias,"#cbo_LaboEmprProvincia",1401);
            appLlenarDataEnComboBox(resp.comboDistritos,"#cbo_LaboEmprDistrito",140101);
            $('#date_LaboInicio').datepicker("setDate",moment(resp.fecha).format("DD/MM/YYYY"));

            document.querySelector("#modLaboTitulo").innerHTML = ("Datos Laborales");
            document.querySelector("#modLaboFormEdit").style.display = 'block';
            document.querySelector("#btn_modLaboInsert").style.display = 'inline';
            document.querySelector("#btn_modLaboUpdate").style.display = 'none';
            $("#modalLabo").modal({keyboard:true});
            $('#modalLabo').on('shown.bs.modal', function() { document.querySelector("#txt_LaboEmpresa").focus(); });
          } catch(err) {
            console.error('Error al cargar datos:', err);
          }
        },
        editar : async function(laboralID){
          try {
            const resp = await appAsynFetch({
              TipoQuery : 'selLaboral',
              ID : laboralID
            }, Laboral.rutaSQL);

            Laboral.datosToForm(resp);
            document.querySelector("#modLaboTitulo").innerHTML = ("Editar Datos Laborales");
            document.querySelector("#modLaboFormEdit").style.display = 'block';
            document.querySelector("#btn_modLaboUpdate").style.display = 'inline';
            document.querySelector("#btn_modLaboInsert").style.display = 'none';
            $("#modalLabo").modal({keyboard:true});
            $('#modalLabo').on('shown.bs.modal', function() { document.querySelector("#txt_LaboEmpresa").focus(); });
          } catch(err){
            console.error('Error al cargar datos:', err);
          }
        },
        borrar : async function(personaID,laboralID){
          try{
            const resp = await appAsynFetch({
              TipoQuery : "ersLaboral",
              commandSQL: 'ERS',
              laboralID : laboralID,
              personaID : personaID
            }, Laboral.rutaSQL);
            return resp;
          } catch(err) {
            console.error('Error al cargar datos:', err);
          }
        },
        comboProvincia : async function(){
          try {
            const resp = await appAsynFetch({
              TipoQuery : "comboUbigeo",
              tipoID  : 3,
              padreID : document.querySelector("#cbo_LaboEmprRegion").value
            }, Laboral.rutaSQL);

            appLlenarDataEnComboBox(resp.provincias,"#cbo_LaboEmprProvincia",0); //provincia
            appLlenarDataEnComboBox(resp.distritos,"#cbo_LaboEmprDistrito",0); //distrito
          } catch(err) {
            console.error('Error al cargar datos:', err);
          }
        },
        comboDistrito : async function(){
          try {
            const resp = await appAsynFetch({
              TipoQuery : "comboUbigeo",
              tipoID  : 4,
              padreID : document.querySelector("#cbo_LaboEmprProvincia").value
            }, Laboral.rutaSQL);

            appLlenarDataEnComboBox(resp.distritos,"#cbo_LaboEmprDistrito",0); //distrito
          } catch(err) {
            console.error('Error al cargar datos:', err);
          }
        },
        sinErrores : function(){
          let Error = true;
          $('.form-group').removeClass('has-error');

          if(document.querySelector("#txt_LaboEmpresa").value.trim()=="") { document.querySelector("#div_LaboEmpresa").className = "form-group has-error"; Error = false; }
          if(document.querySelector("#txt_LaboEmprRubro").value.trim()=="") { document.querySelector("#div_LaboEmprRubro").className = "form-group has-error"; Error = false; }
          if(document.querySelector("#date_LaboInicio").value.trim()=="") { document.querySelector("#div_LaboEmprInicio").className = "form-group has-error"; Error = false; }
          if(document.querySelector("#txt_LaboEmprIngreso").value.trim()=="") { document.querySelector("#div_LaboEmprIngreso").className = "form-group has-error"; Error = false; }
          if(document.querySelector("#txt_LaboEmprDireccion").value.trim()=="") { document.querySelector("#div_LaboEmprDireccion").className = "form-group has-error"; Error = false; }

          return Error;
        },
        datosToDatabase : function(){
          let data = {
            TipoQuery : "execLaboral",
            commandSQL : Laboral.commandSQL,
            ID : Laboral.ID,
            personaID : Laboral.personaID,
            condicion : document.querySelector("#cbo_LaboCondicion").value,
            empresa : document.querySelector("#txt_LaboEmpresa").value.trim().toUpperCase(),
            ruc : document.querySelector("#txt_LaboEmprRUC").value,
            telefono : document.querySelector("#txt_LaboEmprFono").value,
            rubro : document.querySelector("#txt_LaboEmprRubro").value.trim().toUpperCase(),
            distritoID : document.querySelector("#cbo_LaboEmprDistrito").value,
            direccion : document.querySelector("#txt_LaboEmprDireccion").value.trim().toUpperCase(),
            cargo : document.querySelector("#txt_LaboEmprCargo").value.trim().toUpperCase(),
            ingreso : appConvertToNumero(document.querySelector("#txt_LaboEmprIngreso").value),
            fechaini : appConvertToFecha(document.querySelector("#date_LaboInicio").value,""),
            estado: 1,
            observac : document.querySelector("#txt_LaboObservac").value.trim().toUpperCase()
          };
          return data;
        },
        datosToForm : function(data){
          Laboral.commandSQL = "UPD";
          Laboral.ID = data.ID;
          Laboral.personaID = data.id_persona;
          document.querySelector("#cbo_LaboCondicion").value = (data.condicion);
          document.querySelector("#txt_LaboEmpresa").value = (data.empresa);
          document.querySelector("#txt_LaboEmprRUC").value = (data.ruc);
          document.querySelector("#txt_LaboEmprFono").value = (data.telefono);
          document.querySelector("#txt_LaboEmprRubro").value = (data.rubro);
          appLlenarDataEnComboBox(data.comboRegiones,"#cbo_LaboEmprRegion",data.id_region);
          appLlenarDataEnComboBox(data.comboProvincias,"#cbo_LaboEmprProvincia",data.id_provincia);
          appLlenarDataEnComboBox(data.comboDistritos,"#cbo_LaboEmprDistrito",data.id_distrito);
          document.querySelector("#txt_LaboEmprDireccion").value = (data.direccion);
          $('#date_LaboInicio').datepicker("setDate",moment(data.fechaIni).format("DD/MM/YYYY"));
          document.querySelector("#txt_LaboEmprCargo").value = (data.cargo);
          document.querySelector("#txt_LaboEmprIngreso").value = (appFormatMoney(data.ingreso,2));
          document.querySelector("#txt_LaboObservac").value = (data.observLabo);
        },
        ejecutaSQL : async function(){
          try{
            const resp = await appAsynFetch(Laboral.datosToDatabase(), Laboral.rutaSQL);
            return resp;
          } catch(err) {
            console.error('Error al cargar datos:', err);
          }
        },
      };
    return Laboral;
  }
  if(typeof window.Laboral === 'undefined'){ window.Laboral = iniLaboral(); }
})(window,document);
