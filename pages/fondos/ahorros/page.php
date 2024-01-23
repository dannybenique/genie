<?php if(isset($menu->fondos->submenu->ahorros)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-bitbucket"></i> <b>Ahorros</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Ahorros</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="grid">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header no-padding">
          <div class="mailbox-controls">
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:appCreditosReset();"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="DNI, persona..." onkeypress="javascript:appCreditosBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grd">
              <thead>
                <tr>
                  <th style="width:90px;">Fecha</th>
                  <th style="width:110px;" title="Documento Unico de Identidad = DNI, RUC">DUI</th>
                  <th style="">Socio</th>
                  <th style="" title="Producto + TEA%">Producto</th>
                  <th style="">Tipo Credito</th>
                  <th style="width:90px;text-align:right;">Importe</th>
                  <th style="width:90px;text-align:right;">Saldo</th>
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
      <div class="col-md-3">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title" style="font-family:flexoregular;"><b>Credito</b></h3>
          </div>
          <div class="box-body">
            <div class="box-body">
              <input type="hidden" id="hid_crediID" value="">
              <span>Socio:</span> <a id="lbl_crediSocio"></a><br/>
              <span id="lbl_crediTipoDUI"></span>: <a id="lbl_crediNroDUI"></a><br/><br/>
              <span>Inicio:</span> <a id="lbl_crediFecha"></a><br/>
              <span>Producto:</span> <a id="lbl_crediProducto"></a><br/>
              <span>Codigo:</span> <a id="lbl_crediCodigo"></a><br/>
              <span>TEA:</span> <a id="lbl_crediTasa"></a><br/><br/>
              <span>Agencia:</span> <a id="lbl_crediAgencia"></a><br/>
              <span>Promotor:</span> <a id="lbl_crediPromotor"></a><br/>
              <span>Analista:</span> <a id="lbl_crediAnalista"></a><br/><br/>
              <span>Importe:</span> <b><a id="lbl_crediImporte"></a></b><br/>
              <span>Saldo:</span> <b><a id="lbl_crediSaldo"></a></b><br/>
            </div>
            <div class="btn-group pull-left">
              <button id="btnCancel" type="button" class="btn btn-default" onclick="javascript:appCreditosBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            </div>
            <div class="btn-group pull-right">
              <button type="button" class="btn btn-info" id="btn_print" onclick="javascript:appCreditosRefresh();"><i class="fa fa-refresh"></i></button>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-9">
        <div class="box box-primary">
          <div class="box-body box-profile">
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover" id="grdDetalle" style="font-family:helveticaneue_light;">
                  <thead>
                    <tr>
                      <th style="width:30px;">Nro</th>
                      <th style="width:80px;text-align:center;">Fecha</th>
                      <th style="width:95px;text-align:right;">Total</th>
                      <th style="width:95px;text-align:right;">Capital</th>
                      <th style="width:95px;text-align:right;">Interes</th>
                      <th style="width:80px;text-align:right;">Seguro</th>
                      <th style="width:80px;text-align:right;">Gastos</th>
                      <th style="width:100px;text-align:right;">Saldo</th>
                      <th style="width:60px;text-align:center;" title="Retraso en dias">RT</th>
                      <th style="">Doc Pago</th>
                    </tr>
                  </thead>
                  <tbody id="grdDetalleDatos">
                  </tbody>
                </table>
              </div>
          </div>
        </div>
      </div>
    </form>
  </div>
  <div class="modal fade" id="modalAprueba" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form class="form-horizontal" id="frmSoliCredAprueba" autocomplete="off">
          <div class="modal-header" style="background:#f9f9f9;padding:8px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" style="font-family:flexoregular;"><b>Aprobacion de Credito</b></h4>
          </div>
          <div class="modal-body" style="border-right:1px solid white;">
            <div class="box-body row">
              <div class="col-md-6">
                <div class="box-body">
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
                    TEA %: <a id="lbl_modApruebaTEA"></a><br>
                    Desgravamen %: <a id="lbl_modApruebaDesgr"></a><br>
                    Fecha de Inicio: <a id="lbl_modApruebaFechaIniCred"></a><br><br>
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
            <button type="button" class="btn btn-primary" onclick="javascript:modAprueba_BotonAprobar();"><i class="fa fa-flash"></i> Aprobar Solicitud</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<script src="pages/fondos/ahorros/script.js"></script>
<script>
  $(document).ready(function(){
    appCreditosReset();
  });
</script>
<?php } ?>