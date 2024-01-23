<?php if(isset($menu->oper->submenu->solicred)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- modal de Personas-->
<script src="pages/modals/personas/mod.persona.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-file-text"></i> <b>Solicitud de Creditos</b></h1>
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
              <button type="button" id="btn_DEL" class="btn btn-default btn-sm" onclick="javascript:appSoliCredBotonBorrar();"><i class="fa fa-trash"></i></button>
              <button type="button" id="btn_NEW" class="btn btn-default btn-sm" onclick="javascript:appSoliCredBotonNuevo();"><i class="fa fa-plus"></i></button>
            </div>
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:appSoliCredReset();"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="DNI, persona..." onkeypress="javascript:appSoliCredBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
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
                  <th style="width:85px;text-align:center;" title="Fecha de Solicitud">Solicitud</th>
                  <th style="width:110px;" title="Documento Unico de Identidad = DNI, RUC">DUI</th>
                  <th style="">Socio <i class="fa fa-sort"></i></th>
                  <th style="" title="Producto + TEA%">Producto</th>
                  <th style="width:180px;">Tipo Credito</th>
                  <th style="width:85px;text-align:center;" title="Fecha Tentativa del Desembolso">Inicio</th>
                  <th style="width:90px;text-align:right;">Importe</th>
                  <th style="width:50px;text-align:center;" title="Nro de Cuotas">Cuo</th>
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
            <li  class="active"><a href="#datosSoliCred" data-toggle="tab"><i class="fa fa-file-text"></i> Solicitud de Credito</a></li>
            <li><a href="#datosPersonal" data-toggle="tab"><i class="fa fa-address-card"></i> Datos Socio</a></li>
          </ul>
          <div class="tab-content">
            <div id="datosSoliCred" class="tab-pane active">
              <div id="div_SoliCredDatos" class="box-body">
                <div class="col-md-7">
                  <div class="box-body">
                    <div class="form-group" style="margin-bottom:15px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="background:#eeeeee;"><b>Socio</b></span>
                        <input id="txt_SoliCredSocio" type="text" class="form-control" disabled="disabled" />
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Ingreso</b></span>
                        <input id="txt_SoliCredFechaSolici" type="text" class="form-control" style="width:105px;" disabled="disabled" />
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                      <input type="hidden" id="hid_SoliCredID" value=""/>
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Codigo</b></span>
                        <input id="txt_SoliCredCodigo" type="text" class="form-control" placeholder="Codigo..." maxlength="7" disabled="disabled" style="width:150px;"/>
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Moneda</b></span>
                        <select id="cbo_SoliCredMoneda" class="form-control selectpicker" disabled="disabled"></select>
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;" title="Clasificacion Crediticia"><b>Clasif. Cred</b></span>
                        <select id="cbo_SoliCredClasifica" class="form-control selectpicker" disabled="disabled"></select>
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:15px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Condicion</b></span>
                        <select id="cbo_SoliCredCondicion" class="form-control selectpicker" disabled="disabled"></select>
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Agencia</b></span>
                        <select id="cbo_SoliCredAgencia" class="form-control selectpicker"></select>
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Promotor</b></span>
                        <select id="cbo_SoliCredPromotor" class="form-control selectpicker"></select>
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:15px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Analista</b></span>
                        <select id="cbo_SoliCredAnalista" class="form-control selectpicker"></select>
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Tipo SBS</b></span>
                        <select id="cbo_SoliCredTipoSBS" class="form-control selectpicker"></select>
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Destino SBS</b></span>
                        <select id="cbo_SoliCredDestSBS" class="form-control selectpicker"></select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="box-body">
                  <div class="form-group" style="margin-bottom:15px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Tipo Credito</b></span>
                        <select id="cbo_SoliCredTipo" class="form-control selectpicker" onchange="javascript:appSoliCredCambiarTipoCredito();">
                          <option value="1">Fecha Fija</option>
                          <option value="2">Plazo Fijo</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Producto</b></span>
                        <select id="cbo_SoliCredProducto" class="form-control selectpicker"></select>
                      </div>
                    </div>
                    <div id="div_SoliCredImporte" class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Importe</b></span>
                        <input id="txt_SoliCredImporte" type="number" class="form-control" style="width:105px;"/>
                      </div>
                    </div>
                    <div id="div_SoliCredNroCuotas" class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>N° Cuotas</b></span>
                        <input id="txt_SoliCredNroCuotas" type="number" class="form-control" style="width:105px;"/>
                      </div>
                    </div>
                    <div id="div_SoliCredTasa" class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:110px;background:#eeeeee;" title="Tasa Efectiva Anual del Credito"><b>Tasa Credito</b></span>
                        <input id="txt_SoliCredTasa" type="number" class="form-control" style="width:95px;"/>
                        <span class="input-group-addon" style="width:50px;background:#eeeeee;text-align:left;" ><b>% <span style="font-size:10px;">TEA</span></b></span>
                      </div>
                    </div>
                    <div id="div_SoliCredMora" class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:110px;background:#eeeeee;" title="Tasa de MORA Anual"><b>Tasa Mora</b></span>
                        <input id="txt_SoliCredMora" type="number" class="form-control" style="width:95px;"/>
                        <span class="input-group-addon" style="width:50px;background:#eeeeee;text-align:left;" ><b>% <span style="font-size:10px;">TEA</span></b></span>
                      </div>
                    </div>
                    <div id="div_SoliCredSegDesgr" class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:110px;background:#eeeeee;" title="Seguro de desgravamen"><b>Tasa Desgr</b></span>
                        <input id="txt_SoliCredSegDesgr" type="number" class="form-control" style="width:95px;"/>
                        <span class="input-group-addon" style="width:50px;background:#eeeeee;text-align:left;" ><b>% <span style="font-size:10px;">TEA</span></b></span>
                      </div>
                    </div>
                    <div id="div_SoliCredFechaOtorga" class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;" title="Fecha de Inicio"><b>Inicio</b></span>
                        <input id="txt_SoliCredFechaOtorga" type="text" class="form-control" style="width:105px;"/>
                      </div>
                    </div>
                    <div id="div_SoliCredFechaPriCuota" class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;" title="Fecha para la primera cuota"><b>1° Cuota</b></span>
                        <input id="txt_SoliCredFechaPriCuota" type="text" class="form-control" style="width:105px;"/>
                      </div>
                    </div>
                    <div id="div_SoliCredFrecuencia" class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;" title="Frecuencia de pago en dias"><b>Frecuencia</b></span>
                        <input id="txt_SoliCredFrecuencia" type="text" class="form-control" style="width:105px;" disabled="disabled"/>
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:3px;">
                      <div class="input-group">
                        <div class="input-group-btn">
                          <button type="button" class="btn btn-primary" style="width:101px;" onclick="javacript:appSoliCredGenerarPlanPagos();">Cuota S/.</button>
                        </div>
                        <input id="txt_SoliCredCuota" type="text" class="form-control" disabled="disabled" style="width:105px;"/>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="input-group">
                        <textarea id="txt_SoliCredObserv" type="text" placeholder="Observaciones de solicitud..." cols="100" rows="5" style="width:100%;"></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="box-body">
                    <button id="btnCancel" type="button" class="btn btn-default" onclick="javascript:appSoliCredBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
                    <button id="btnInsert" type="button" class="btn btn-primary pull-right" onclick="javascript:appSoliCredBotonInsert();"><i class="fa fa-save"></i> Guardar</button>
                    <button id="btnUpdate" type="button" class="btn btn-info pull-right" onclick="javascript:appSoliCredBotonUpdate();"><i class="fa fa-save"></i> Actualizar</button>
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
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form class="form-horizontal" id="frmSoliCredAprueba" autocomplete="off">
          <div class="modal-header" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" style="font-family:flexoregular;font-weight:bold;">Aprobacion de Credito</h4>
          </div>
          <div class="modal-body" style="border-right:1px solid white;">
            <div class="box-body row">
              <div class="col-md-6">
                <div class="box-body">
                  <strong><i class="fa fa-thumbs-up margin-r-5"></i> Aprobacion</strong>
                  <div class="form-group" style="margin-bottom:15px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#eeeeee;"><b>Fecha</b></span>
                      <input id="txt_modApruebaFechaAprueba" type="text" class="form-control" style="width:105px;" disabled="disabled" />
                    </div>
                  </div>
                  <strong><i class="fa fa-pencil margin-r-5"></i> Basicos</strong>
                  <p class="text-muted">
                    <input type="hidden" id="hid_modApruebaID" value=""/>
                    Socio: <a id="lbl_modApruebaSocio"></a><br>
                    Fecha Solicitud: <a id="lbl_modApruebaFechaSoliCred"></a><br>
                    Codigo: <a id="lbl_modApruebaCodigo"></a><br>
                    Moneda: <a id="lbl_modApruebaMoneda"></a><br>
                    Clasif. Cred.: <a id="lbl_modApruebaClasifica"></a><br>
                    Condición: <a id="lbl_modApruebaCondicion"></a><br>
                  </p>
                  <hr/>

                  <strong><i class="fa fa-briefcase margin-r-5"></i> Agencia</strong>
                  <p class="text-muted">
                    Agencia: <a id="lbl_modApruebaAgencia"></a><br>
                    Promotor: <a id="lbl_modApruebaPromotor"></a><br>
                    Analista: <a id="lbl_modApruebaAnalista"></a><br>
                  </p>
                  <hr/>

                  <strong><i class="fa fa-gavel margin-r-5"></i> SBS</strong>
                  <p class="text-muted">
                    Tipo: <a id="lbl_modApruebaTipoSBS"></a><br>
                    Destino: <a id="lbl_modApruebaDestinoSBS"></a>
                  </p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="box-body">
                  <strong><i class="fa fa-gg-circle margin-r-5"></i> Credito</strong>
                  <p class="text-muted">
                    Tipo Credito: <a id="lbl_modApruebaTipoCredito"></a><br>
                    Producto: <a id="lbl_modApruebaProducto"></a><br>
                    Importe: <a id="lbl_modApruebaImporte"></a><br>
                    N° Cuotas: <a id="lbl_modApruebaNrocuotas"></a><br>
                    Tasa Credito: <a id="lbl_modApruebaTasaCred"></a><a>% <span style="font-size:10px;">(TEA)</span></a><br>
                    Tasa Mora: <a id="lbl_modApruebaTasaMora"></a><a>% <span style="font-size:10px;">(TEA)</span></a><br>
                    Tasa Desgr: <a id="lbl_modApruebaTasaDesgr"></a><a>% <span style="font-size:10px;">(TEA)</span></a><br>
                    Fecha Inicio: <a id="lbl_modApruebaFechaOtorga"></a><br>
                    1° Cuota: <a id="lbl_modApruebaFechaPriCuota"></a><br>
                    <span id="lbl_modEtiqFrecuencia" style="display:none;">Frecuencia: <a id="lbl_modApruebaFrecuencia"></a><br></span><br>
                    Cuota: <span id="lbl_modApruebaCuota" class="badge bg-green" style="font-weight:normal;font-size:14px;"></span>
                  </p>
                  <hr/>

                  <strong><i class="fa fa-file-text-o margin-r-5"></i> Observaciones</strong>
                  <p class="text-muted">
                    <span id="lbl_modApruebaObservac"></span>
                  </p><br><br>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            <button type="button" class="btn btn-primary" onclick="javascript:modAprueba_BotonAprobar();"><i class="fa fa-thumbs-up"></i> Aprobar Solicitud</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<script src="pages/oper/solicred/script.js"></script>
<script>
  $(document).ready(function(){
    Persona.addModalToParentForm('modalPers');
    appSoliCredReset();
  });
</script>
<?php } ?>