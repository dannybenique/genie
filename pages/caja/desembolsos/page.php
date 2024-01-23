<?php if(isset($menu->caja->submenu->desemb)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-wrench"></i> <b>Desembolso</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">desembolso</li>
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
                  <th style="width:85px;text-align:center;" title="Fecha de Solicitud">Solicitud</th>
                  <th style="">Socio <i class="fa fa-sort"></i></th>
                  <th style="" title="Producto + TEA%">Producto</th>
                  <th style="width:180px;">Tipo Credito</th>
                  <th style="width:85px;text-align:center;" title="Fecha Tentativa del Desembolso">Inicio T.</th>
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
            <li  class="active"><a href="#datosSoliCred" data-toggle="tab"><i class="fa fa-briefcase"></i> Solicitud de Credito</a></li>
            <li><a href="#datosPersonal" data-toggle="tab"><i class="fa fa-male"></i> Datos Socio</a></li>
          </ul>
          <div class="tab-content">
            <div id="datosSoliCred" class="tab-pane active">
              <div class="box-body">
                <div class="form-group" style="margin-left:0px;margin-bottom:3px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Desembolso</b></span>
                    <input id="txt_DesembFecha" type="text" class="form-control" style="width:105px;" disabled="disabled" />
                  </div>
                </div>
              </div>
              <div class="box-body row">
                <div class="col-md-6">
                  <div class="box-body">
                    <strong><i class="fa fa-pencil margin-r-5"></i> Basicos</strong>
                    <p class="text-muted">
                      <input type="hidden" id="hid_tipocredID" value=""/>
                      <input type="hidden" id="hid_productoID" value=""/>
                      <input type="hidden" id="hid_agenciaID" value=""/>
                      <input type="hidden" id="hid_monedaID" value=""/>
                      <input type="hidden" id="hid_socioID" value=""/>
                      Socio: <a id="lbl_DesembSocio"></a><br>
                      Solicitud: <a id="lbl_DesembFechaSoliCred"></a><br>
                      ID: <a id="lbl_DesembPrestamoID"></a><br>
                      Codigo: <a id="lbl_DesembCodigo"></a><br>
                      Moneda: <a id="lbl_DesembMoneda"></a><br>
                      Clasif. Cred. : <a id="lbl_DesembClasifi"></a><br>
                      Condicion: <a id="lbl_DesembCondicion"></a><br>
                    </p>
                    <hr/>

                    <strong><i class="fa fa-briefcase margin-r-5"></i> Agencia</strong>
                    <p class="text-muted">
                      Agencia: <a id="lbl_DesembAgencia"></a><br>
                      Promotor: <a id="lbl_DesembPromotor"></a><br>
                      Analista: <a id="lbl_DesembAnalista"></a><br>
                      <span id="lbl_FormAprueba">Aprobacion:</span> <a id="lbl_DesembAprueba"></a><br>
                    </p>
                    <hr/>

                    <strong><i class="fa fa-legal margin-r-5"></i> SBS</strong>
                    <p class="text-muted">
                      Tipo: <a id="lbl_DesembTipoSBS"></a><br>
                      Destino: <a id="lbl_DesembDestSBS"></a><br>
                    </p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="box-body">
                    <strong><i class="fa fa-gg-circle margin-r-5"></i> Credito</strong>
                    <p class="text-muted">
                      Tipo: <a id="lbl_DesembTipoCred"></a><br><br>
                      Producto: <a id="lbl_DesembProducto"></a><br>
                      Importe: <a id="lbl_DesembImporte"></a><br>
                      N° Cuotas: <a id="lbl_DesembNrocuotas"></a><br>
                      Tasa Credito: <a id="lbl_DesembTasaCred"></a><a>% <span style="font-size:10px;">(TEA)</span></a><br>
                      Tasa Mora: <a id="lbl_DesembTasaMora"></a><a>% <span style="font-size:10px;">(TEA)</span></a><br>
                      Tasa Desgr.: <a id="lbl_DesembTasaDesgr"></a><a>% <span style="font-size:10px;">(TEA)</span></a><br>
                      Fecha Inicio: <a id="lbl_DesembFechaOtorga"></a><br>
                      Fecha 1° Cuota: <a id="lbl_DesembFechaPriCuota"></a><br>
                      <span id="lbl_DesembEtqFrecuencia" style="display:none;">Frecuencia: <a id="lbl_DesembFrecuencia"></a><br></span><br>
                      Cuota: <span id="lbl_DesembCuota" class="badge bg-green" style="font-weight:normal;font-size:14px;"></span>
                    </p>
                    <hr/>

                    <strong><i class="fa fa-file-text-o margin-r-5"></i> Observaciones</strong>
                    <p class="text-muted">
                      <a id="lbl_DesembObservac"></a><br>
                    </p>
                  </div>
                  <div class="box-body">
                    <button id="btnCancel" type="button" class="btn btn-default" onclick="javascript:appDesembBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
                    <button id="btnInsert" type="button" class="btn btn-primary pull-right" onclick="javascript:appDesembBotonDesembolsar();"><i class="fa fa-flash"></i> Desembolsar</button>
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