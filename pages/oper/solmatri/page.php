<?php if(isset($menu->oper->submenu->solmatri)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- modal de Personas-->
<script src="pages/modals/personas/mod.persona.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-file-text"></i> <b>Solicitud de Matricula</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Solicitud</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <div class="btn-group">
              <button type="button" id="btn_DEL" class="btn btn-default btn-sm" onclick="javascript:appSolMatriBotonBorrar();"><i class="fa fa-trash"></i></button>
              <button type="button" id="btn_NEW" class="btn btn-default btn-sm" onclick="javascript:appSolMatriBotonNuevo();"><i class="fa fa-plus"></i></button>
            </div>
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:appSolMatriReset();"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="DNI, persona..." onkeypress="javascript:appSolMatriBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grd">
              <thead>
                <tr>
                  <th style="width:25px;"><input type="checkbox" id="chk_All" onclick="toggleAll(this,'chk_Borrar');" /></th>
                  <th style="width:30px;" title="Aprobacion de Solicitud"><i class="fa fa-thumbs-up"></i></th>
                  <th style="width:120px;text-align:center;" title="Codigo de Matricula">Codigo</th>
                  <th style="width:85px;text-align:center;" title="Fecha de Solicitud">Solicitud</th>
                  <th style="width:95px;" title="Documento Unico de Identidad = DNI, RUC">DNI</th>
                  <th style="">Alumno <i class="fa fa-sort"></i></th>
                  <th style="" title="Grado y Seccion">Grado</th>
                </tr>
              </thead>
              <tbody id="grdDatos"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row" id="edit" style="display:none;">
    <form class="form-horizontal" id="frmPersona" autocomplete="off">
      <div class="col-md-9">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
            <li  class="active"><a href="#datosSolMatri" data-toggle="tab"><i class="fa fa-file-text"></i> Solicitud de Matricula</a></li>
            <li><a href="#datosPersonal" data-toggle="tab"><i class="fa fa-address-card"></i> Datos Alumno</a></li>
          </ul>
          <div class="tab-content">
            <div id="datosSolMatri" class="tab-pane active">
              <div id="div_SolMatriDatos" class="box-body">
                <div style="padding:0 24px 0 24px;">
                  <div class="form-group" style="margin-bottom:15px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#eeeeee;"><b>Alumno</b></span>
                      <input id="txt_SolMatriAlumno" type="text" class="form-control" disabled="disabled" />
                    </div>
                  </div>
                </div>
                <div class="col-md-7">
                  <div class="box-body">
                    <div id="div_SolMatriFechaSolicita" class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Solicita</b></span>
                        <input id="txt_SolMatriFechaSolicita" type="text" class="form-control" style="width:105px;" disabled="disabled" />
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                      <input type="hidden" id="hid_SolMatriID" value=""/>
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Codigo Matr.</b></span>
                        <input id="txt_SolMatriCodigo" type="text" class="form-control" placeholder="Codigo Matricula..." disabled="disabled" style="width:150px;"/>
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:15px;">
                      <div class="input-group">
                      <input type="hidden" id="hid_SolMatriID" value=""/>
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Año Matr.</b></span>
                        <select id="cbo_SolMatriYYYY" class="form-control selectpicker"></select>
                      </div>
                    </div>

                    <div class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Nivel</b></span>
                        <select id="cbo_SolMatriNiveles" class="form-control selectpicker" onchange="javascript:comboGrados();"></select>
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Grado</b></span>
                        <select id="cbo_SolMatriGrados" class="form-control selectpicker" onchange="javascript:comboSecciones();"></select>
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Seccion</b></span>
                        <select id="cbo_SolMatriSecciones" class="form-control selectpicker"></select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="box-body">
                    <div class="form-group">
                      <div class="input-group">
                        <textarea id="txt_SolMatriObservac" type="text" placeholder="Observaciones de solicitud..." cols="100" rows="5" style="width:100%;"></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="box-body">
                    <button id="btnCancel" type="button" class="btn btn-default" onclick="javascript:appSolMatriBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
                    <button id="btnInsert" type="button" class="btn btn-primary pull-right" onclick="javascript:appSolMatriBotonInsert();"><i class="fa fa-save"></i> Guardar</button>
                    <button id="btnUpdate" type="button" class="btn btn-info pull-right" onclick="javascript:appSolMatriBotonUpdate();"><i class="fa fa-save"></i> Actualizar</button>
                  </div>
                </div>
              </div>
            </div>
            <div id="datosPersonal" class="tab-pane">
              <div class="box-body row">
                <div class="col-md-5">
                  <div class="box-body">
                    <strong><i class="fa fa-address-card margin-r-5"></i> Basicos</strong>
                    <p class="text-muted">
                      <input type="hidden" id="hid_PersID" value=""/>
                      <span id="lbl_PersTipoNombres">Nombres</span>: <a id="lbl_PersNombres"></a><br>
                      <span id="lbl_PersTipoApellidos">Apellidos: <a id="lbl_PersApellidos"></a><br></span><br>
                      <span id="lbl_PersTipoDNI"></span>: <a id="lbl_PersNroDNI"></a><br>
                      Pais Nac: <a id="lbl_PersPaisNac"></a><br>
                      Lugar Nac: <a id="lbl_PersLugarNac"></a><br>
                      Fecha Nac: <a id="lbl_PersFechaNac"></a><br>
                      Edad: <a id="lbl_PersEdad"></a><br>
                      <span id="lbl_PersTipoSexo">Sexo: <a id="lbl_PersSexo"></a><br></span>
                      <span id="lbl_PersTipoECivil">Estado Civil: <a id="lbl_PersEcivil"></a></span>
                    </p>
                    <hr/>

                    <strong><i class="fa fa-phone margin-r-5"></i> Contacto</strong>
                    <p class="text-muted">
                      Celular: <a id="lbl_PersCelular"></a><br>
                      Telefono Fijo: <a id="lbl_PersTelefijo"></a><br>
                      Correo: <a id="lbl_PersEmail"></a><br>
                    </p>
                    <hr/>

                    <strong><i class="fa fa-graduation-cap margin-r-5"></i> Profesionales</strong>
                    <p class="text-muted">
                      <span id="lbl_PersTipoGIntruc">Grado Instruccion: <a id="lbl_PersGInstruccion"></a><br></span>
                      <span id="lbl_PersTipoProfesion">Profesion</span>: <a id="lbl_PersProfesion"></a><br>
                      Ocupacion: <a id="lbl_PersOcupacion"></a>
                    </p>
                  </div>
                </div>
                <div class="col-md-7">
                  <div class="box-body">
                    <strong><i class="fa fa-map-marker margin-r-5"></i> Ubicacion</strong>
                      <table class="table-responsive no-padding">
                        <tr>
                          <td style="width:65px;vertical-align:bottom;">Direccion:</td>
                          <td><a id="lbl_PersUbicacion" style="font:12px configcondensed_light;"></a><br>
                              <a id="lbl_PersDireccion"></a></td>
                        </tr>
                      </table>
                      Referencia: <a id="lbl_PersReferencia"></a><br>
                      Medidor de Luz: <a id="lbl_PersMedidorluz"></a><br>
                      Medidor de Agua: <a id="lbl_PersMedidorAgua"></a><br>
                      Tipo de Vivienda: <a id="lbl_PersTipovivienda"></a>
                    <hr/>

                    <strong><i class="fa fa-book margin-r-5"></i> Observaciones</strong>
                    <p class="text-muted">
                      <span id="lbl_PersObservac"></span>
                    </p><br><br>
                    <div id="div_PersAuditoria">
                      <strong><i class="fa fa-eye margin-r-5"></i> Auditoria</strong>
                      <div style="font:10px flexolight;color:gray;">
                        Fecha: <span id="lbl_PersSysFecha"></span><br>
                        Modif. por: <span id="lbl_PersSysUser"></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
  <div class="modal fade" id="modalPers" role="dialog"></div>
  <div class="modal fade" id="modalAprueba" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form class="form-horizontal" id="frmSolMatriAprueba" autocomplete="off">
          <div class="modal-header" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" style="font-family:flexoregular;font-weight:bold;">Aprobacion de Matricula</h4>
          </div>
          <div class="modal-body" style="border-right:1px solid white;">
            <div class="box-body row">
              <div class="col-md-12">
                <div class="box-body">
                  <strong><i class="fa fa-thumbs-up margin-r-5"></i> Aprobacion</strong>
                  <div class="form-group" style="margin-bottom:15px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#eeeeee;"><b>Fecha</b></span>
                      <input id="txt_modapruebaFechaAprueba" type="text" class="form-control" style="width:105px;" disabled="disabled" />
                    </div>
                  </div><br>
                  <strong><i class="fa fa-user margin-r-5"></i> Basicos</strong>
                  <p class="text-muted">
                    <input type="hidden" id="hid_modapruebaID" value=""/>
                    Alumno: <a id="lbl_modapruebaAlumno"></a><br>
                    DNI: <a id="lbl_modapruebaDNI"></a><br>
                  </p>
                  <hr/>

                  <strong><i class="fa fa-gg-circle margin-r-5"></i> Matricula</strong>
                  <p class="text-muted">
                    Fecha Solicitud: <a id="lbl_modapruebaFechaSolMatri"></a><br>
                    Codigo: <a id="lbl_modapruebaCodigo"></a><br>
                    Nivel: <a id="lbl_modapruebaNivel"></a><br>
                    Grado: <a id="lbl_modapruebaGrado"></a><br>
                    Seccion: <a id="lbl_modapruebaSeccion"></a>
                  </p>
                  <hr/>
                  
                  <strong><i class="fa fa-file-text-o margin-r-5"></i> Observaciones</strong>
                  <p class="text-muted">
                    <span id="lbl_modapruebaObservac"></span>
                  </p><br>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            <button type="button" class="btn btn-primary" onclick="javascript:modaprueba_BotonAprobar();"><i class="fa fa-thumbs-up"></i> Aprobar Solicitud</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<script src="pages/oper/solmatri/script.js"></script>
<script>
  $(document).ready(function(){
    Persona.addModalToParentForm('modalPers');
    appSolMatriReset();
  });
</script>
<?php } ?>