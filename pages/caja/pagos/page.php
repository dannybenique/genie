<?php if(isset($menu->caja->submenu->pagos)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gg-circle"></i> <b>Pagos</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">pagos</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row" id="edit">
    <div class="col-md-4">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title" style="font-family:flexoregular;"><b>Opciones</b></h3>
        </div>
        <div class="box-body">
          <button type="button" id="btn_NEW" class="btn btn-info btn-sm" onclick="javascript:appPagosBotonNuevo();"><i class="fa fa-plus"></i> Nuevo pago</button>
          <button type="button" class="btn btn-primary btn-sm pull-right" onclick="javascript:appPagosReset();"><i class="fa fa-refresh"></i></button>
        </div>
      </div>
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title" style="font-family:flexoregular;"><b>Credito</b></h3>
        </div>
        <div class="box-body">
          <div class="box-body">
            Socio: <a id="lbl_crediSocio"></a><br/>
            <span id="lbl_crediTipoDUI">DUI</span>: <a id="lbl_crediNroDUI"></a><br/><br/>
            Desembolso: <a id="lbl_crediFecha"></a><br/>
            Producto: <a id="lbl_crediProducto"></a><br/>
            Codigo: <a id="lbl_crediCodigo"></a><br/>
            tasa Credito: <a id="lbl_crediTasaCred"></a><br/>
            tasa Mora: <a id="lbl_crediTasaMora"></a><br/>
            Moneda: <a id="lbl_crediMoneda"></a><br/><br/>
            Agencia: <a id="lbl_crediAgencia"></a><br/>
            Promotor: <a id="lbl_crediPromotor"></a><br/>
            Analista: <a id="lbl_crediAnalista"></a><br/><br/>
            Atraso: <b><a id="lbl_crediAtraso"></a></b><br/>
            Importe: <b><a id="lbl_crediImporte"></a></b><br/>
            Saldo: <b><a id="lbl_crediSaldo"></a></b><br/><br/>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title" style="font-family:flexoregular;"><b>Deuda pendiente</b></h3>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="box-body">
              <div class="box-body">
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#eeeeee;"><b>CAPITAL</b></span>
                      <input id="txt_DeudaCapital" type="text" class="form-control" disabled="disabled"/>
                    </div>
                  </div>
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#eeeeee;"><b>INTERES</b></span>
                      <input id="txt_DeudaInteres" type="text" class="form-control" disabled="disabled"/>
                    </div>
                  </div>
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#eeeeee;"><b>MORA</b></span>
                      <input id="txt_DeudaMora" type="text" class="form-control" disabled="disabled"/>
                    </div>
                  </div>
                  <div class="form-group" style="margin-bottom:5px;">
                    <div class="input-group">
                      <span class="input-group-addon" style="background:#eeeeee;"><b>OTROS</b></span>
                      <input id="txt_DeudaOtros" type="text" class="form-control" disabled="disabled"/>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="box-body">
              <div class="box-body">
                <div class="form-group" style="margin-bottom:10px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>FECHA PAGO</b></span>
                    <input id="txt_DeudaFecha" type="text" class="form-control" disabled="disabled" style="width:120px;"/>
                  </div>
                </div>
              </div>
              <div class="box-body">
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>DEUDA NETA</b></span>
                    <input id="txt_DeudaTotalNeto" type="text" class="form-control" disabled="disabled"/>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>MONEDA</b></span>
                    <select id="cbo_DeudaMonedas" class="form-control selectpicker" disabled="disabled"></select>
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>MEDIO DE PAGO</b></span>
                    <select id="cbo_DeudaMedioPago" class="form-control selectpicker"></select>
                  </div>
                </div>
                <div id="div_DeudaImporte" class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>IMPORTE A PAGAR</b></span>
                    <input id="txt_DeudaImporte" type="text" class="form-control" autocomplete="off"/>
                  </div>
                </div>
              </div>
              <div class="box-body">
                <button type="button" id="btn_PAGAR" class="btn btn-success btn-sm pull-right" onclick="javascript:appPagosBotonPagar();"><i class="fa fa-flash"></i> Pagar Deuda</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalCredi" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 id="modalCredi_Titulo" class="modal-title" style="font-family:flexoregular;font-weight:bold;">Datos Credito</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon no-border">Nro Documento</span>
                  <input type="number" id="modalCredi_TxtBuscar" class="form-control" placeholder="DNI, RUC..." onkeypress="javascript:modalCredi_keyBuscar(event);">
                  <div class="input-group-btn" style="height:30px;">
                    <button type="button" class="btn btn-primary" onclick="javacript:modalCrediBuscar();"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
              <div class="box-body table-responsive no-padding">
                <span id="modalCredi_Wait"></span>
                <div id="modalCredi_Grid">
                  <table class="table table-hover">
                    <tr>
                      <th style="width:110px;">DUI</th>
                      <th style="">Socio</th>
                      <th style="">Producto</th>
                      <th style="width:100px;text-align:right;">Importe</th>
                      <th style="width:100px;text-align:right;">Saldo</th>
                    </tr>
                    <tbody id="modalCredi_GridBody"></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
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

<script src="pages/caja/pagos/script.js"></script>
<script>
  $(document).ready(function(){
    appPagosReset();
  });
</script>
<?php } ?>