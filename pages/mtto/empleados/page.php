<?php if(isset($menu->mtto->submenu->empleados)){?>
<!-- encriptar con sha1 -->
<script type="text/javascript" src="libs/webtoolkit/webtoolkit.sha1.js"></script>

<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- ztree menu -->
<link rel="stylesheet" href="libs/ztree/css/ztreestyle.css" />
<script src="libs/ztree/js/jquery.ztree.core.min.js"></script>
<script src="libs/ztree/js/jquery.ztree.excheck.min.js"></script>
<script src="libs/ztree/js/jquery.ztree.exedit.min.js"></script>

<!-- modal de Personas y Laborales y Conyuge -->
<script src="pages/modals/personas/mod.persona.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gear"></i> <b>Empleados</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Empleados</li>
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
              <button type="button" id="btn_DEL" class="btn btn-default btn-sm" onclick="javascript:appWorkersBotonBorrar();"><i class="fa fa-trash"></i></button>
              <button type="button" id="btn_NEW" class="btn btn-default btn-sm" onclick="javascript:appWorkersBotonNuevo();"><i class="fa fa-plus"></i></button>
            </div>
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:appWorkersReset();"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="DNI, persona..." onkeypress="javascript:appWorkersBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
            <a class="pull-right" href="javascript:appWorkersBotonViewAll();" title="ver todos los registros"><i id="icoViewAll" class="fa fa-toggle-off"></i><input type="hidden" id="hidViewAll" value="0"></a>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grd">
              <thead>
                <tr>
                  <th style="width:25px;"><input type="checkbox" id="chk_All" onclick="toggleAll(this,'chk_Borrar');" /></th>
                  <th style="width:30px;"><i class="fa fa-paperclip"></i></th>
                  <th style="width:30px;"><i class="fa fa-lock"></i></th>
                  <th style="width:80px;">Codigo</th>
                  <th style="width:110px;" title="Documento Unico de Identidad = DNI, RUC">DUI</th>
                  <th style="">Empleado <i class="fa fa-sort"></i></th>
                  <th style="">Nombre Corto</th>
                  <th style="">Cargo</th>
                  <th style=""></th>
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
        <div class="box box-widget widget-user-2">
          <div class="widget-user-header"  style="background:#f9f9f9;">
            <div class="widget-user-image">
              <input type="hidden" id="hid_PersUrlFoto" value=""/>
              <img class="profile-user-img img-circle" src="" id="img_Foto" alt="persona"/>
            </div>
            <div style="min-height:70px;">
              <h4 class="widget-user-username" id="lbl_Apellidos"></h4>
              <h4 class="widget-user-desc" id="lbl_Nombres"></h4>
            </div>
          </div>
          <div class="no-padding">
            <ul class="list-group">
              <li class="list-group-item" ><b>ID</b> <a class="pull-right" id="lbl_ID" style="font:14px flexoregular;"></a></li>
              <li class="list-group-item"><b id="lbl_TipoDNI">DNI</b> <a class="pull-right" id="lbl_DNI" style="font:14px flexoregular;"></a></li>
              <li class="list-group-item"><b>Celular</b><a class="pull-right" id="lbl_Celular" style="font:14px flexoregular;"></a></li>
              <li class="list-group-item" ><b>Codigo</b> <a class="pull-right" id="lbl_Codigo" style="font:14px flexoregular;"></a></li>
            </ul>
          </div>
          <div class="box-body">
            <button id="btnCancel" type="button" class="btn btn-default" onclick="javascript:appWorkersBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            <button id="btnInsert" type="button" class="btn btn-primary pull-right" onclick="javascript:appWorkersBotonInsert();"><i class="fa fa-save"></i> Guardar</button>
            <button id="btnUpdate" type="button" class="btn btn-info pull-right" onclick="javascript:appWorkersBotonUpdate();"><i class="fa fa-save"></i> Actualizar</button>
          </div>
        </div>
      </div>
      <div class="col-md-9">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#datosPersonal" data-toggle="tab"><i class="fa fa-address-card"></i> Personal</a></li>
            <li><a href="#datosWorker" data-toggle="tab"><i class="fa fa-briefcase"></i> Empleado</a></li>
            <li><a href="#datosUsuario" data-toggle="tab"><i class="fa fa-user"></i> Usuario</a></li>
          </ul>
          <div class="tab-content">
            <div id="datosPersonal" class="tab-pane active">
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
                    </div><br>
                    <button id="btn_PersUpdate" type="button" class="btn btn-primary btn-xs" onclick="javascript:appPersonaEditar();"><i class="fa fa-flash"></i> Modificar Datos</button>
                    <button id="btn_PersPermiso" type="button" class="btn btn-success btn-xs" style="display:none;" onclick="javascript:appPermisoPersonas();"><i class="fa fa-shield"></i> Solicitar Permiso</button>
                  </div>
                </div>
              </div>
            </div>
            <div id="datosWorker" class="tab-pane">
              <div class="box-body">
                <div class="col-md-6">
                  <div class="box-body">
                    <div class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:120px;background:#eeeeee;"><b>Codigo</b></span>
                        <input id="txt_WorkerCodigo" type="text" class="form-control" placeholder="Codigo..." maxlength="4" disabled="disabled" style="width:105px;text-align:center;"/>
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:120px;background:#eeeeee;"><b>Fecha Ingreso</b></span>
                        <input id="txt_WorkerFechaIng" type="text" class="form-control" style="width:105px;" disabled="disabled" />
                      </div>
                    </div>
                    <div id="div_WorkerNombreCorto" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:120px;background:#eeeeee;"><b>Nombre Corto</b></span>
                        <input id="txt_WorkerNombreCorto" type="text" class="form-control" placeholder="nombre corto..." maxlength="30" style="width:200px;"/>
                      </div>
                    </div>
                    <div id="div_WorkerCorreo" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:120px;background:#eeeeee;" title="Correo Institucional"><b>Correo Inst.</b></span>
                        <input id="txt_WorkerCorreo" type="text" class="form-control" placeholder="correo..." maxlength="50" style="width:200px;"/>
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:85px;background:#eeeeee;"><b>Cargo</b></span>
                        <select id="cbo_WorkerCargo" class="form-control selectpicker"></select>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="input-group">
                        <textarea id="txt_WorkerObserv" type="text" placeholder="Observaciones de socio..." cols="80" rows="10" style="width:100%;"></textarea>
                      </div>
                    </div>
                    <div id="div_WorkerAuditoria">
                      <strong><i class="fa fa-eye margin-r-5"></i> Auditoria</strong>
                      <div style="font:10px flexolight;color:gray;">
                        Fecha: <span id="lbl_WorkerSysFecha"></span><br>
                        Modif. por: <span id="lbl_WorkerSysUser"></span>
                      </div>
                    </div><br>
                  </div>
                </div>
              </div>
            </div>
            <div id="datosUsuario" class="tab-pane">
              <div class="box-body">
                <div class="col-md-12">
                  <div class="box-body">
                    <div class="form-group">
                      <div class="input-group">
                        <span class="input-group-addon pull-left" style="border:1px solid #D2D6DD;width:100%;padding:9px 10px 9px 10px;">
                          <input id="chk_UserEsUsuario" type="checkbox" value="0" onchange="appUserEsUsuario();"> Si, es usuario del sistema
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="box-body">
                    <strong><i class="fa fa-user"></i> Usuario</strong>
                    <div id="div_UserLogin" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Login</b></span>
                        <input id="txt_UserLogin" type="email" class="form-control" placeholder="login..." maxlength="50" autocomplete="off"/>
                      </div>
                    </div>
                    <div id="div_UserPassword" class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Password</b></span>
                        <input id="txt_UserPassword" type="password" class="form-control" maxlength="20" autocomplete="off"/>
                      </div>
                    </div>
                    <div id="div_UserRePassword" class="form-group" style="margin-bottom:20px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Repass</b></span>
                        <input id="txt_UserRePassword" type="password" class="form-control" maxlength="20" autocomplete="off"/>
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:20px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:100px;background:#eeeeee;"><b>Rol</b></span>
                        <select id="cbo_UserRol" class="form-control selectpicker"></select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-7">
                  <div class="box-body">
                    <strong><i class="fa fa-sliders"></i> Menu</strong>
                    <div class="box-body" style="padding-left:0px">
                      <button id="btn_UserPerfilRoot" type="button" class="btn btn-primary btn-xs" onclick="javascript:appUserPerfilMenu(1);"><i class="fa fa-user"></i> Root</button>
                      <button id="btn_UserPerfilCaja" type="button" class="btn btn-primary btn-xs" onclick="javascript:appUserPerfilMenu(2);"><i class="fa fa-user"></i> Caja</button>
                    </div>
                    <div id="div_UserMenu" class="box-body table-responsive no-padding" style="border:1px solid #ccc;">
                        <ul id="appTreeView" class="ztree"></ul>
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
  <div class="modal fade" id="modalChangePassw" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" style="font-family:flexoregular;"><b>Cambiar Password</b></h4>
        </div>
        <form class="form-horizontal" id="frmChangePassw" autocomplete="off">
          <div class="modal-body no-padding">
            <div class="box-body">
              <h4 class="timeline-header no-border" style="font-family:flexoregular;">
                <div style="padding-bottom:10px;">
                  <span class="appSpanPerfil">Usuario</span>
                  <span id="lbl_PassNombrecorto" style="color:#3c8dbc;font-weight:600;"></span>
                </div>
                <div>
                  <span class="appSpanPerfil">Login</span>
                  <span id="lbl_PassLogin" style="color:#3c8dbc;font-weight:600;"></span>
                </div>
              </h4>
            </div>
            <div class="box-body">
              <div class="col-md-12">
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>Nuevo Password</b></span>
                    <input type="password" class="form-control" id="txt_PassPassNew" placeholder="password..." autocomplete="off">
                  </div>
                </div>
                <div class="form-group" style="margin-bottom:5px;">
                  <div class="input-group">
                    <span class="input-group-addon" style="background:#eeeeee;"><b>Repetir Password</b></span>
                    <input type="password" class="form-control" id="txt_PassPassRe" placeholder="repetir password..." autocomplete="off">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
            <input type="hidden" id="hid_PassID" value="">
            <input type="hidden" id="hid_PassColegioID" value="">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            <button type="button" class="btn btn-primary" onclick="javascript:modUserBotonUpdatePassw();"><i class="fa fa-flash"></i> Cambiar Password</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<script src="pages/mtto/empleados/script.js"></script>
<script>
  $(document).ready(function(){
    Persona.addModalToParentForm('modalPers');
    appWorkersReset();
  });
</script>
<?php } ?>