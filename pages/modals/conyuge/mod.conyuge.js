//=========================funciones para crear el modal de laboral============================
(function (window,document){
  var iniConyuge = function(){
      var Conyuge = {
        rutaSQL : "pages/modals/conyuge/mod.sql.php",
        rutaHTML : "pages/modals/conyuge/mod.conyuge.htm",
        personaID : 0,
        conyugeID : 0,
        laboralID : 0,
        commandSQL : "",
        addModalToParentForm : function(contenedor) { $("#"+contenedor).load(Conyuge.rutaHTML); },
        close : function(){ $("#modalCony").modal("hide"); },
        lnkConyuge : function(){
          Persona.openBuscar('VerifyConyuge',Conyuge.rutaSQL,true,true,false);

          $('#btn_modPersInsert').on('click',function(e) {
            if(Persona.sinErrores()){
              Persona.ejecutaSQL().then(rpta => {
                let data = {
                  id_conyuge : rpta.tablaPers.ID,
                  tiempoRelacion : 1,
                  persona : rpta.tablaPers
                }
                Persona.close();
                Conyuge.datosToForm(data);
                $("#modalCony").modal();
                $('#btn_modConyLaboral').show();
              });
            } else {
              alert("!!!Faltan llenar Datos!!!");
            }
            e.stopImmediatePropagation();
            $('#btn_modPersInsert').off('click');
          });
          $('#btn_modPersUpdate').on('click',function(e) {
            console.log("ingreso por update aun no esta definido");
            e.stopImmediatePropagation();
            $('#btn_modPersUpdate').off('click');
          });
          $('#btn_modPersAddToForm').on('click',function(e) {
            let otro = {
              id_conyuge : Persona.tablaPers.ID,
              tiempoRelacion : 1,
              persona : Persona.tablaPers
            }
            //console.log(otro);
            Persona.close();
            Conyuge.datosToForm(otro);
            $("#modalCony").modal();
            $('#btn_modConyLaboral').show();
            e.stopImmediatePropagation();
            $('#btn_modPersAddToForm').off('click');
          });
        },
        nuevo : function(personaID){
          let datos = {
            id_conyuge : 0,
            tiempoRelacion : 1
          }
          Conyuge.commandSQL = "INS";
          Conyuge.personaID = personaID;
          Conyuge.datosToForm(datos);
          document.querySelector("#modConyTitulo").innerHTML = ("Nuevo Datos Conyugales");
          document.querySelector("#btn_modConyInsert").style.display = 'inline';
          document.querySelector("#btn_modConyUpdate").style.display = 'none';
          Conyuge.lnkConyuge();
        },
        editar : function(personaID){
          let datos = {
            TipoQuery : 'selConyuge',
            personaID : personaID
          }
          appFetch(datos,Conyuge.rutaSQL).then(resp => {
            Conyuge.commandSQL = "UPD";
            Conyuge.personaID = personaID;
            Conyuge.datosToForm(resp);
            document.querySelector("#modConyTitulo").innerHTML = ("Editar Datos Conyugales");
            document.querySelector("#btn_modConyUpdate").style.display = 'inline';
            document.querySelector("#btn_modConyInsert").style.display = 'none';
            $("#modalCony").modal();
          });
        },
        borrar : function(personaID){
          let datos = {
            TipoQuery : "delConyuge",
            commandSQL: "DEL",
            personaID : personaID
          }
          let exec = new FormData();
          exec.append("appSQL",JSON.stringify(datos));
          let rpta = fetch(Conyuge.rutaSQL, { method:'POST', body:exec })
            .then(rpta => rpta.json())
            .catch(err => console.log(err));
          return rpta;
          /*
          let rpta = $.ajax({
            url  : Conyuge.rutaSQL,
            type : 'POST',
            processData : false,
            contentType : false,
            data : exec
          })
          .fail(function(resp){
            console.log("fail:.... "+resp.responseText);
          });
          return rpta;
          */
        },
        sinErrores : function(){
          let Error = true;
          $('.form-group').removeClass('has-error');
          if(document.querySelector("#txt_modConyTiempoRela").value.trim()=="") { document.querySelector("#div_modConyTiempoRela").className = "form-group has-error"; Error = false; }
          if(Conyuge.conyugeID==0) { Error = false; }
          return Error;
        },
        datosToDatabase : function(){
          let data = {
            TipoQuery : "execConyuge",
            commandSQL : Conyuge.commandSQL,
            personaID : Conyuge.personaID,
            conyugeID : Conyuge.conyugeID,
            permisoID : document.querySelector('#hid_modConyPermisoID').value,
            tiempoRelacion : document.querySelector("#txt_modConyTiempoRela").value
          };
          return data;
        },
        datosToForm : function(data){
          //datos personales
          Conyuge.conyugeID = data.id_conyuge;
          if(data.id_conyuge>0){
            document.querySelector('#lbl_modConyNombres').innerHTML = (data.persona.nombres);
            document.querySelector('#lbl_modConyApellidos').innerHTML = (data.persona.ap_paterno+" "+data.persona.ap_materno);
            document.querySelector('#lbl_modConyNroDNI').innerHTML = (data.persona.nroDUI);
            document.querySelector('#lbl_modConyFechaNac').innerHTML = (moment(data.persona.fechanac).format("DD/MM/YYYY"));
            document.querySelector('#lbl_modConyEcivil').innerHTML = (data.persona.ecivil);
          } else {
            document.querySelector('#lbl_modConyNombres').innerHTML = ("");
            document.querySelector('#lbl_modConyApellidos').innerHTML = ("");
            document.querySelector('#lbl_modConyNroDNI').innerHTML = ("");
            document.querySelector('#lbl_modConyFechaNac').innerHTML = ("");
            document.querySelector('#lbl_modConyEcivil').innerHTML = ("");
          }

          //datos relacion
          document.querySelector("#txt_modConyTiempoRela").value = (data.tiempoRelacion);
        },
        ejecutaSQL : function(){
          let exec = new FormData();
          let datos = Conyuge.datosToDatabase();

          exec.append("appSQL",JSON.stringify(datos));
          let rpta = fetch(Conyuge.rutaSQL, { method:'POST', body:exec })
            .then(rpta => rpta.json())
            .catch(err => console.log(err));
          return rpta;
          /*
          let rpta = $.ajax({
            url  : Conyuge.rutaSQL,
            type : 'POST',
            processData : false,
            contentType : false,
            data : exec
          })
          .fail(function(resp){
            console.log("fail:.... "+resp.responseText);
          });
          return rpta;
          */
        },
      };
    return Conyuge;
  }
  if(typeof window.Conyuge === 'undefined'){ window.Conyuge = iniConyuge(); }
})(window,document);
