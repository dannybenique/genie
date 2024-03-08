//=========================funciones para crear el modal de personas============================
(function (window,document){
  var inicio = function(){
      var Persona = {
        rutaSQL : "pages/modals/personas/mod.sql.php",
        rutaHTML : "pages/modals/personas/mod.persona.htm",
        personaID : 0,
        commandSQL : "",
        queryBuscar : "",
        queryURL : "",
        tipoPersona : 0, //1: natural; 2: juridica
        addNewPers : false, //permite añadir nuevos en personas
        addNewLista : false, //permite añadir nuevos en la lista
        addRepLista : false, //permite añadir repetidos en lista
        tablaPers : 0,
        addModalToParentForm : function(contenedor) { $("#"+contenedor).load(Persona.rutaHTML); },
        close : function(){ $("#modalPers").modal("hide"); },
        calcularEdad: function() {
          let hoy = moment(); //fecha hoy
          let nac = moment(appConvertToFecha(document.querySelector("#date_modPersFechanac").value,"-")); //fecha nac
          document.querySelector("#lbl_modPersEdad").innerHTML = (hoy.diff(nac,"years"));
        },
        keyBuscar: function(e){
          let code = (e.keyCode ? e.keyCode : e.which);
          if(code == 13) { Persona.buscar(); }
        },
        openBuscar : function(query,url,addNewPers,addNewLista,addRepLista){
          Persona.queryBuscar = query;
          Persona.queryURL = url;
          Persona.addNewPers = addNewPers; 
          Persona.addNewLista = addNewLista; 
          Persona.addRepLista = addRepLista; 
          document.querySelector("#modPersTitulo").innerHTML = ("Verificar Doc. Identidad");
          document.querySelector("#modPersFormEdit").style.display = 'none';
          document.querySelector("#modPersGridDatosTabla").style.display = 'none';
          document.querySelector("#modPersFormGrid").style.display = 'block';
          document.querySelector("#lbl_modPersWait").innerHTML = ("");
          document.querySelector("#txt_modPersBuscar").value = ("");
          $("#modalPers").modal({keyboard:true});
          $('#modalPers').on('shown.bs.modal', function() { document.querySelector("#txt_modPersBuscar").focus(); });
        },
        buscar : async function(){
          try{
            let nroDUI = document.querySelector("#txt_modPersBuscar").value.trim();
            if(nroDUI.length>=8){
              document.querySelector('#lbl_modPersWait').innerHTML = ('<div class="progress progress-xs active"><div class="progress-bar progress-bar-success progress-bar-striped" style="width:100%"></div></div>');
              const resp = await appAsynFetch({
                TipoQuery : Persona.queryBuscar,
                nroDNI : nroDUI
              }, Persona.queryURL);
              
              document.querySelector('#lbl_modPersWait').innerHTML = ("");
              document.querySelector('#lbl_modPersDUI').innerHTML = (nroDUI);
              document.querySelector("#modPersGridDatosTabla").style.display = 'block';

              if(resp.persona){ //existe en Personas
                Persona.tablaPers = resp.tablaPers;
                document.querySelector('#btn_modPersAddToPersonas').style.display = 'none';
                switch(resp.activo){
                  case true: // YA existe en la lista
                    document.querySelector('#btn_modPersAddToForm').style.display = ((Persona.addRepLista)?('inline'):('none'));
                    document.querySelector('#lbl_modPersPersona').innerHTML = ((Persona.addRepLista)
                      ?(resp.tablaPers.persona+" &raquo; "+resp.tablaPers.direccion)
                      :("La "+((resp.tablaPers.tipoPersona==1)?('persona '):('entidad '))+"<b>"+(resp.tablaPers.persona)+"</b> identificada con <b>"+(resp.tablaPers.tipoDUI)+'-'+(resp.tablaPers.nroDUI)+"</b> "+(resp.mensajeNOadd)));
                    break;
                  case false: // NO existe en la lista
                    document.querySelector('#btn_modPersAddToForm').style.display = ((Persona.addNewLista)?('inline'):('none'));
                    document.querySelector('#lbl_modPersPersona').innerHTML = (resp.tablaPers.persona+" &raquo; "+resp.tablaPers.direccion+' '+((Persona.addNewLista)?(''):('No puede ser agregado a esta lista')));
                    break;
                  case 2: //no debe ser añadido
                    document.querySelector('#btn_modPersAddToForm').style.display = 'none';
                    document.querySelector('#lbl_modPersPersona').innerHTML = (resp.tablaPers.persona+' &raquo; '+resp.tablaPers.direccion+' No puede ser agregado a esta lista');
                    break;
                }
              } else { //NO existe en Personas
                document.querySelector('#btn_modPersAddToForm').style.display = 'none';
                document.querySelector('#btn_modPersAddToPersonas').style.display = ((Persona.addNewPers)?('block'):('none')); //permite añadir nuevas personas segun config
                document.querySelector('#lbl_modPersPersona').innerHTML = ('No existe la persona identificada con nro <b>'+nroDUI+'</b> y deseo Agregarla');
              }
            } else { alert("!!!El Nro de DNI debe ser de 08 digitos y el RUC de 11 digitos!!!"); }
          } catch (err){
            console.error('Error al cargar datos:', err);
          }
        },
        nuevo : async function(){
          try {
            document.querySelector('#lbl_modPersWait').innerHTML = ('<div class="progress progress-sm active"><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:100%"></div></div>');
            const nroDNI = document.querySelector("#txt_modPersBuscar").value.trim();
            const resp = await appAsynFetch( { TipoQuery:'newPersona' }, Persona.rutaSQL);
            document.querySelector('#lbl_modPersWait').innerHTML = ("");

            //pestaña de datos personales
            Persona.commandSQL = "INS";
            Persona.personaID = 0;
            Persona.tablaPers = 0;
            document.querySelector('#cbo_modPersTipoPers').value = (1);
            document.querySelector('#hid_modPersPermisoID').value = (0);
            document.querySelector('#hid_modPersUrlFoto').value = ("");
            document.querySelector("#txt_modPersNombres").value = ("");
            document.querySelector("#txt_modPersApePaterno").value = ("");
            document.querySelector("#txt_modPersApeMaterno").value = ("");
            document.querySelector("#txt_modPersDocumento").value = (nroDNI);
            document.querySelector("#txt_modPersCelular").value = ("");
            document.querySelector("#txt_modPersFijo").value = ("");
            document.querySelector("#txt_modPersEmail").value = ("");
            document.querySelector("#txt_modPersProfesion").value = ("");
            document.querySelector("#txt_modPersOcupacion").value = ("");
            document.querySelector("#txt_modPersLugarnac").value = ("");
            document.querySelector("#lbl_modPersEdad").innerHTML = ("0");
            document.querySelector("#txt_modPersDireccion").value = ("");
            document.querySelector("#txt_modPersReferencia").value = ("");
            document.querySelector("#txt_modPersMedidorLuz").value = ("");
            document.querySelector("#txt_modPersMedidorAgua").value = ("");
            document.querySelector("#txt_modPersObserv").value = ("");
            appLlenarDataEnComboBox(resp.comboPais,"#cbo_modPersPaisnac",101);
            appLlenarDataEnComboBox(resp.comboDUI,"#cbo_modPersDocumento",0);
            appLlenarDataEnComboBox(resp.comboSexo,"#cbo_modPersSexo",0);
            appLlenarDataEnComboBox(resp.comboECivil,"#cbo_modPersEcivil",0);
            appLlenarDataEnComboBox(resp.comboGInstruc,"#cbo_modPersGinstruc",0);
            appLlenarDataEnComboBox(resp.comboTipoViv,"#cbo_modPersTipoVivienda",0);
            appLlenarDataEnComboBox(resp.comboRegiones,"#cbo_modPersRegion",1014); //region arequipa
            appLlenarDataEnComboBox(resp.comboProvincias,"#cbo_modPersProvincia",1401); //provincia arequipa
            appLlenarDataEnComboBox(resp.comboDistritos,"#cbo_modPersDistrito",140101); //distrito arequipa
            $('#date_modPersFechanac').datepicker("setDate",moment(resp.fecha).format("DD/MM/YYYY"));
            
            //config inicial
            document.querySelector("#txt_modPersNombres").placeholder = 'NOMBRES';
            document.querySelector("#div_modPersApePaterno").style.display = 'block';
            document.querySelector("#div_modPersApeMaterno").style.display = 'block';
            document.querySelector("#div_modPersGinstruc").style.display = 'block';
            document.querySelector("#div_modPersSexoEcivil").style.display = 'block';
            document.querySelector("#cbo_modPersDocumento").removeAttribute('disabled');

            document.querySelector("#modPersFormGrid").style.display = 'none';
            document.querySelector("#modPersFormEdit").style.display = 'block';
            document.querySelector("#btn_modPersUpdate").style.display = 'none';
            document.querySelector("#btn_modPersInsert").style.display = 'inline';
            //$("#modalPers").modal();
          } catch(err){
            console.error('Error al cargar datos:', err);
          }
        },
        editar : async function(personaID,tipoPers){
          try {
            Persona.tipoPersona = tipoPers;
            const resp = await appAsynFetch({
              TipoQuery : 'selPersona',
              personaID : personaID
            }, Persona.rutaSQL);
  
            Persona.datosToForm(resp);
            document.querySelector("#modPersTitulo").innerTHML = ("Datos Personales &raquo; "+personaID);
            document.querySelector("#modPersFormGrid").style.display = 'none';
            document.querySelector("#modPersFormEdit").style.display = 'block';
            document.querySelector("#btn_modPersInsert").style.display = 'none';
            document.querySelector("#btn_modPersUpdate").style.display = 'inline';
            $("#modalPers").modal({keyboard:true});
            $('#modalPers').on('shown.bs.modal', function() { $('#txt_modPersNombres').trigger('focus') });
          } catch(err){
            console.error('Error al cargar datos:', err);
          }
        },
        comboProvincia : async function(){
          try{
            const resp = await appAsynFetch({
              TipoQuery : "comboUbigeo",
              tipoID  : 3, //provincias
              padreID : document.querySelector("#cbo_modPersRegion").value
            }, Persona.rutaSQL);

            appLlenarDataEnComboBox(resp.provincias,"#cbo_modPersProvincia",0); //provincia
            appLlenarDataEnComboBox(resp.distritos,"#cbo_modPersDistrito",0); //distrito
          } catch(err){
            console.error('Error al cargar datos:', err);
          }
        },
        comboDistrito : async function(){
          try {
            const resp = await appAsynFetch({
              TipoQuery : "comboUbigeo",
              tipoID  : 4, //distritos
              padreID : document.querySelector("#cbo_modPersProvincia").value
            }, Persona.rutaSQL);

            appLlenarDataEnComboBox(resp.distritos,"#cbo_modPersDistrito",0); //distrito
          } catch(err){
            console.error('Error al cargar datos:', err);
          }
        },
        apidni : async function(){
          try {
            const nroDoc = document.querySelector("#txt_modPersDocumento").value;
            const resp = await fetch("https://dniruc.apisperu.com/api/v1/"+(((nroDoc.length==8)?("dni/"):((nroDoc.length==11)?("ruc/"):("")))+(nroDoc))+"?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImRhbm55YmVuaXF1ZUBtc24uY29tIn0.ts3qFRsLtLxqnoOMvwYEeOu470tyTUGWQbsuH4ZTC7I");
            const rpta = await resp.json();
            
            if(rpta.success){
              document.querySelector("#txt_modPersNombres").value = (rpta.nombres);
              document.querySelector("#txt_modPersApePaterno").value = (rpta.apellidoPaterno);
              document.querySelector("#txt_modPersApeMaterno").value = (rpta.apellidoMaterno);  
            } else {
              alert("NO hay datos desde la API");
            }
          } catch(err) {
            console.log(err);
            alert("Hubo un error con la API");
          }
        },
        sinErrores : function(){
          let rpta = true;
          $('.form-group').removeClass('has-error');

          switch (document.querySelector("#cbo_modPersTipoPers").value) {
            case "1":
              if(document.querySelector("#txt_modPersNombres").value.trim()=="") { document.querySelector("#div_modPersNombres").className = "form-group has-error"; rpta = false; }
              if(document.querySelector("#txt_modPersDocumento").value.trim()=="") { document.querySelector("#div_modPersDocumento").className = "form-group has-error"; rpta = false; }
              if(document.querySelector("#txt_modPersApePaterno").value.trim()=="") { document.querySelector("#div_modPersApePaterno").className = "form-group has-error"; rpta = false; }
              if(document.querySelector("#txt_modPersApeMaterno").value.trim()=="") { document.querySelector("#div_modPersApeMaterno").className = "form-group has-error"; rpta = false; }
              break;
            case "2":
              if(document.querySelector("#txt_modPersNombres").value.trim()=="") { document.querySelector("#div_modPersNombres").className = "form-group has-error"; rpta = false; }
              if(document.querySelector("#txt_modPersDocumento").value.trim()=="") { document.querySelector("#div_modPersDocumento").className = "form-group has-error"; rpta = false; }
              break;
          }
          return rpta;
        },
        datosToDatabase : function(){
          const datosPers = {
            TipoQuery : ((Persona.personaID==0)?("insPersona"):("updPersona")),
            commandSQL : Persona.commandSQL,
            ID : Persona.personaID,
            persPermisoID : document.querySelector('#hid_modPersPermisoID').value,
            persTipoPersona : document.querySelector("#cbo_modPersTipoPers").value,
            persNombres : document.querySelector("#txt_modPersNombres").value.trim().toUpperCase(),
            persApePaterno : document.querySelector("#txt_modPersApePaterno").value.trim().toUpperCase(),
            persApeMaterno : document.querySelector("#txt_modPersApeMaterno").value.trim().toUpperCase(),
            persNroDUI : document.querySelector("#txt_modPersDocumento").value.trim(),
            persId_DUI : document.querySelector("#cbo_modPersDocumento").value,
            persId_sexo : document.querySelector("#cbo_modPersSexo").value,
            persId_Ginstruc : document.querySelector("#cbo_modPersGinstruc").value,
            persId_Ecivil : document.querySelector("#cbo_modPersEcivil").value,
            persId_Ubigeo : document.querySelector("#cbo_modPersDistrito").value,
            persId_TipoVivienda : document.querySelector("#cbo_modPersTipoVivienda").value,
            persId_Paisnac : document.querySelector("#cbo_modPersPaisnac").value,
            persFechaNac : appConvertToFecha(document.querySelector("#date_modPersFechanac").value.trim(),""),
            persLugarNac : document.querySelector("#txt_modPersLugarnac").value.trim().toUpperCase(),
            persTelefijo : document.querySelector("#txt_modPersFijo").value.trim(),
            persCelular : document.querySelector("#txt_modPersCelular").value.trim(),
            persEmail : document.querySelector("#txt_modPersEmail").value.trim().toLowerCase(),
            persProfesion : document.querySelector("#txt_modPersProfesion").value.trim().toUpperCase(),
            persOcupacion : document.querySelector("#txt_modPersOcupacion").value.trim().toUpperCase(),
            persDireccion : document.querySelector("#txt_modPersDireccion").value.trim().toUpperCase(),
            persReferencia : document.querySelector("#txt_modPersReferencia").value.trim().toUpperCase(),
            persMedidorluz : document.querySelector("#txt_modPersMedidorLuz").value.trim(),
            persMedidorAgua : document.querySelector("#txt_modPersMedidorAgua").value.trim(),
            persUrlFoto : document.querySelector('#hid_modPersUrlFoto').value.trim(),
            persObservac : document.querySelector("#txt_modPersObserv").value.trim().toUpperCase()
          };
          return datosPers;
        },
        datosToForm : function(data){
          Persona.commandSQL = "UPD";
          Persona.personaID = data.ID;
          //$('#hid_modPersPermisoID').val(data.permisoPersona.ID);
          document.querySelector('#hid_modPersUrlFoto').value = (data.urlfoto);
          document.querySelector('#cbo_modPersTipoPers').value = (data.tipoPersona);
          document.querySelector("#txt_modPersNombres").value = (data.nombres);
          document.querySelector("#txt_modPersApePaterno").value = (data.ap_paterno);
          document.querySelector("#txt_modPersApeMaterno").value = (data.ap_materno);
          document.querySelector("#txt_modPersDocumento").value = (data.nroDUI);
          document.querySelector("#txt_modPersCelular").value = (data.celular);
          document.querySelector("#txt_modPersFijo").value = (data.telefijo);
          document.querySelector("#txt_modPersEmail").value = (data.correo);
          document.querySelector("#txt_modPersProfesion").value = (data.profesion);
          document.querySelector("#txt_modPersOcupacion").value = (data.ocupacion);
          document.querySelector("#txt_modPersLugarnac").value = (data.lugarnac);
          $('#date_modPersFechanac').datepicker("setDate",moment(data.fechanac).format("DD/MM/YYYY"));
          appLlenarDataEnComboBox(data.comboPais,"#cbo_modPersPaisnac",data.id_paisnac);
          appLlenarDataEnComboBox(data.comboDUI,"#cbo_modPersDocumento",data.id_dui);
          appLlenarDataEnComboBox(data.comboSexo,"#cbo_modPersSexo",data.id_sexo);
          appLlenarDataEnComboBox(data.comboECivil,"#cbo_modPersEcivil",data.id_ecivil);
          appLlenarDataEnComboBox(data.comboGInstruc,"#cbo_modPersGinstruc",data.id_ginstruc);
          appLlenarDataEnComboBox(data.comboTipoViv,"#cbo_modPersTipoVivienda",data.id_tipovivienda);
          appLlenarDataEnComboBox(data.comboRegiones,"#cbo_modPersRegion",data.id_region);
          appLlenarDataEnComboBox(data.comboProvincias,"#cbo_modPersProvincia",data.id_provincia);
          appLlenarDataEnComboBox(data.comboDistritos,"#cbo_modPersDistrito",data.id_distrito);
          document.querySelector("#txt_modPersDireccion").value = (data.direccion);
          document.querySelector("#txt_modPersReferencia").value = (data.referencia);
          document.querySelector("#txt_modPersMedidorLuz").value = (data.medidorluz);
          document.querySelector("#txt_modPersMedidorAgua").value = (data.medidoragua);
          document.querySelector("#txt_modPersObserv").value = (data.observPers);
          document.querySelector('#file_modPersFoto').value = (null);
          Persona.calcularEdad();
          if(data.tipoPersona==2){ //persona juridica
            document.querySelector("#div_modPersApePaterno").style.display = 'none';
            document.querySelector("#div_modPersApeMaterno").style.display = 'none';
            document.querySelector("#div_modPersGinstruc").style.display = 'none';
            document.querySelector("#div_modPersSexoEcivil").style.display = 'none';
            document.querySelector("#cbo_modPersDocumento").removeAttribute('disabled','disabled');
            document.querySelector("#txt_modPersNombres").placeholder = 'RAZON SOCIAL';
          } else {
            document.querySelector("#txt_modPersNombres").placeholder = 'NOMBRES';
            document.querySelector("#div_modPersApePaterno").style.display = 'block';
            document.querySelector("#div_modPersApeMaterno").style.display = 'block';
            document.querySelector("#div_modPersGinstruc").style.display = 'block';
            document.querySelector("#div_modPersSexoEcivil").style.display = 'block';
            document.querySelector("#cbo_modPersDocumento").removeAttribute('disabled');
          }
        },
        ejecutaSQL : async function(){
          let foto = $('#file_modPersFoto')[0].files[0];
          let exec = new FormData();
          exec.append('imgFoto', foto);
          exec.append("appSQL",JSON.stringify(Persona.datosToDatabase()));
          
          try {
            const resp = await fetch(Persona.rutaSQL, { method:'POST', body:exec });
            const rpta = await resp.json();
            return rpta;
          } catch(err){
            console.log(err);
          }
        }
      };
    return Persona;
  }
  if(typeof window.Persona === 'undefined'){ window.Persona = inicio(); }
})(window,document);
