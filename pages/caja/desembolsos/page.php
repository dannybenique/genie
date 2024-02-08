<?php if(isset($menu->caja->submenu->desembolsos)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-wrench"></i> <b>Ejecutar Matricula</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Ejecutar</li>
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
              <button type="button" id="btn_DEL" class="btn btn-default btn-sm" onclick="javascript:appDesembBotonBorrar();"><i class="fa fa-trash-o"></i></button>
            </div>
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:appDesembReset();"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="DNI, persona..." onkeypress="javascript:appDesembBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grd">
              <thead>
                <tr>
                  <th style="width:25px;"><input type="checkbox" id="chk_All" onclick="toggleAll(this,'chk_Borrar');" /></th>
                  <th style="width:120px;text-align:center;" title="Codigo de Matricula">Codigo</th>
                  <th style="width:85px;text-align:center;" title="Fecha de Solicitud">Solicitud</th>
                  <th style="width:85px;text-align:center;" title="Fecha de Aprobacion">Aprobacion</th>
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
      <div class="col-md-3">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title" style="font-family:flexoregular;"><b>Datos de Solicitud</b></h3>
          </div>
          <div class="box-body">
            <p class="text-muted">
              <input type="hidden" id="hid_DesembID" value=""/>
              Codigo: <a id="lbl_DesembCodigo"></a><br><br>
              Nivel: <a id="lbl_DesembNivel"></a><br>
              Grado: <a id="lbl_DesembGrado"></a><br>
              Seccion: <a id="lbl_DesembSeccion"></a><br><br>
              Alumno: <a id="lbl_DesembAlumno"></a><br>
              DNI: <a id="lbl_DesembAlumnoDNI"></a><br><br>
              Importe: <a id="lbl_DesembImporte"></a>
            </p>
            <hr/>
            <button id="btnCancel" type="button" class="btn btn-default" onclick="javascript:appDesembBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            <button id="btnInsert" type="button" class="btn btn-primary pull-right" onclick="javascript:appDesembBotonDesembolsar();"><i class="fa fa-flash"></i> Matricular</button>
          </div>
        </div>
      </div>
      <div class="col-md-9">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
            <li  class="active"><a href="#datosMatricula" data-toggle="tab"><i class="fa fa-briefcase"></i> Aprobacion</a></li>
            <li><a href="#datosPersonal" data-toggle="tab"><i class="fa fa-male"></i> Datos Alumno</a></li>
          </ul>
          <div class="tab-content">
            <div id="datosMatricula" class="tab-pane active">
              <div class="box-body row">
                <div class="col-md-6">
                  <div class="box-body">
                      <strong><i class="fa fa-thumbs-up margin-r-5"></i> Ejecucion</strong>
                      <div class="form-group" style="margin-bottom:15px;">
                        <div class="input-group">
                          <span class="input-group-addon" style="background:#eeeeee;"><b>Fecha</b></span>
                          <input id="txt_DesembFecha" type="text" class="form-control" style="width:105px;" disabled="disabled" />
                        </div>
                      </div>
                      Solicitud: <a id="lbl_DesembFechaSolicita"></a><br>
                      Aprobacion: <a id="lbl_DesembFechaAprueba"></a><br><br>
                      <strong><i class="fa fa-file-text-o margin-r-5"></i> Observaciones</strong>
                      <p class="text-muted">
                        <span id="lbl_DesembObservac"></span>
                      </p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="box-body">
                    <strong><i class="fa fa-ticket margin-r-5"></i> Pagos</strong>
                    <div class="box-body table-responsive no-padding">
                      <table class="table table-hover">
                        <thead>
                          <tr>
                            <th style="width:25px;"><input type="checkbox" id="chk_All" onclick="toggleAll(this,'chk_BorrarPagos');" /></th>
                            <th style="width:45px;" title="Abreviatura">Abr</th>
                            <th style="">Pago <i class="fa fa-sort"></i></th>
                            <th style="width:85px;text-align:center;" title="Fecha de Vencimiento">Vcmto</th>
                            <th style="text-align:right;" title="Costo">Importe</th>
                          </tr>
                        </thead>
                        <tbody id="grdPagos"></tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="datosPersonal" class="tab-pane">
              <div class="box-body row">
                <div class="col-md-5">
                  <div class="box-body">
                    <strong><i class="fa fa-user margin-r-5"></i> Basicos</strong>
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

                    <strong><i class="fa fa-file-text-o margin-r-5"></i> Observaciones</strong>
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
  <div class="modal fade" id="modalPrint" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" style="font-family:flexoregular;"><b>Imprimir Voucher</b></h4>
          </div>
          <div class="modal-body" style="border-right:1px solid white;">
            <div class="table-responsive no-padding" id="contenedorFrame">
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
          </div>
      </div>
    </div>
  </div>
</section>

<script src="pages/caja/desembolsos/script.js"></script>
<script>
  $(document).ready(function(){
    appDesembReset();
  });
</script>
<?php } ?>