<?php if(isset($menu->mtto->submenu->padres)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- modal de Personas y Laborales y Conyuge -->
<script src="pages/modals/personas/mod.persona.js"></script>
<script src="pages/modals/laboral/mod.laboral.js"></script>
<script src="pages/modals/conyuge/mod.conyuge.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gear"></i> <b>Padres</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Padres</li>
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
              <button type="button" id="btn_DEL" class="btn btn-default btn-sm" onclick="javascript:appPadresBotonBorrar();"><i class="fa fa-trash"></i></button>
              <button type="button" id="btn_NEW" class="btn btn-default btn-sm" onclick="javascript:appPadresBotonNuevo();"><i class="fa fa-plus"></i></button>
            </div>
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:appPadresReset();"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="DNI, persona..." onkeypress="javascript:appPadresBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grd">
              <thead>
                <tr>
                  <th style="width:25px;"><input type="checkbox" id="chk_All" onclick="toggleAll(this,'chk_Borrar');" /></th>
                  <th style="width:25px;"><i class="fa fa-paperclip" title="Auditoria"></i></th>
                  <th style="width:110px;">DNI</th>
                  <th style="">Persona <i class="fa fa-sort"></i></th>
                  <th style="">Direccion</th>
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
              <li class="list-group-item" ><b>Codigo</b> <a class="pull-right" id="lbl_Codigo" style="font:14px flexoregular;"></a></li>
            </ul>
          </div>
          <div class="box-body">
            <button id="btnCancel" type="button" class="btn btn-default" onclick="javascript:appPadresBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            <button id="btnInsert" type="button" class="btn btn-primary pull-right" onclick="javascript:appPadresBotonInsert();"><i class="fa fa-save"></i> Guardar</button>
            <button id="btnUpdate" type="button" class="btn btn-info pull-right" onclick="javascript:appPadresBotonUpdate();"><i class="fa fa-save"></i> Actualizar</button>
          </div>
        </div>
      </div>
      <div class="col-md-9">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#datosPersonal" data-toggle="tab"><i class="fa fa-address-card"></i> Personal</a></li>
            <li><a href="#datosLaboral" data-toggle="tab"><i class="fa fa-steam"></i> Laboral</a></li>
            <li><a href="#datosConyuge" data-toggle="tab"><i class="fa fa-heart"></i> Conyuge</a></li>
          </ul>
          <div class="tab-content">
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
                    </div><br>
                    <button id="btn_PersUpdate" type="button" class="btn btn-primary btn-xs" onclick="javascript:appPersonaEditar();"><i class="fa fa-flash"></i> Modificar Datos</button>
                    <button id="btn_PersPermiso" type="button" class="btn btn-success btn-xs" style="display:none;" onclick="javascript:appPermisoPersonas();"><i class="fa fa-shield"></i> Solicitar Permiso</button>
                  </div>
                </div>
              </div>
            </div>
            <div id="datosLaboral" class="tab-pane">
              <div class="box-body row" id="div_LaboGrid">
                <div class="box-body">
                  <button id="btn_LaboInsert" type="button" class="btn btn-primary btn-xs" onclick="javascript:appLaboralNuevo();"><i class="fa fa-plus"></i> Agregar Datos Laborales</button>
                  <button id="btn_LaboPermiso" type="button" class="btn btn-success btn-xs" style="display:none;" onclick="javascript:appPermisoLaboral();"><i class="fa fa-shield"></i> Solicitar Permiso</button>
                </div>
                <div class="box-body table-responsive no-padding">
                  <table class="table table-hover" id="grdLaboDatos">
                    <thead>
                      <tr>
                        <th style="width:20px;" title="Eliminar"><i class="fa fa-trash"></i></th>
                        <th style="width:105px;">Condicion</th>
                        <th style="width:105px;">RUC</th>
                        <th style="">Empresa</th>
                        <th style="">Cargo</th>
                        <th style="width:105px;text-align:right;">Ingreso</th>
                      </tr>
                    </thead>
                    <tbody id="grdLaboDatosBody">
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="box-body row" id="div_LaboEdit" style="display:none;">
                <input type="hidden" id="hid_LaboPermisoID" value="0"/>
                <div class="col-md-6">
                  <div class="box-body">
                    <strong><i class="fa fa-truck"></i> Laborales</strong>
                    <p class="text-muted">
                      Condicion: <a id="lbl_LaboCondicion"></a><br>
                      Empresa: <a id="lbl_LaboEmprRazon"></a><br>
                      RUC: <a id="lbl_LaboEmprRUC"></a><br>
                      Telefono: <a id="lbl_LaboEmprFono"></a><br>
                      Rubro: <a id="lbl_LaboEmprRubro"></a><br><br>

                      Fecha Ing.: <a id="lbl_LaboEmprFechaIng"></a><br>
                      Cargo: <a id="lbl_LaboEmprCargo"></a><br>
                      Ingreso (S/.): <a id="lbl_LaboEmprIngreso"></a>
                    </p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="box-body">
                    <strong><i class="fa fa-map-marker"></i> Ubicacion</strong>
                    <p class="text-muted">
                      Direccion: <a id="lbl_LaboEmprDireccion"></a> &raquo; (<a id="lbl_LaboEmprUbicacion"></a>)<br>
                    </p>
                    <hr/>

                    <strong><i class="fa fa-file-text-o margin-r-5"></i> Observaciones</strong>
                    <p class="text-muted">
                      <span id="lbl_LaboEmprObservac"></span>
                    </p><br><br>
                    <strong><i class="fa fa-eye margin-r-5"></i> Auditoria</strong>
                    <div style="font-style:italic;font-size:11px;color:gray;">
                      Fecha: <span id="lbl_LaboSysFecha"></span><br>
                      Modif. por: <span id="lbl_LaboSysUser"></span>
                    </div>
                  </div>
                  <button id="btn_LaboUpdate" type="button" class="btn btn-primary btn-xs" onclick="javascript:appLaboralEditar();"><i class="fa fa-flash"></i> Modificar Datos</button>
                  <button id="btn_LaboDelete" type="button" class="btn btn-danger btn-xs" style="display:none;" onclick="javascript:appLaboralDelete();"><i class="fa fa-trash-o"></i> Quitar Laboral</button>
                </div>
              </div>
            </div>
            <div id="datosConyuge" class="tab-pane">
              <div class="box-body row">
                <div class="box-body">
                  <button id="btn_ConyInsert"  type="button" class="btn btn-primary btn-xs" style="display:none;" onclick="javascript:appConyugeNuevo();"><i class="fa fa-plus"></i> Agregar Conyuge</button>
                  <button id="btn_ConyUpdate"  type="button" class="btn btn-primary btn-xs" style="display:none;" onclick="javascript:appConyugeEditar();"><i class="fa fa-flash"></i> Modificar Datos</button>
                  <button id="btn_ConyDelete"  type="button" class="btn btn-danger btn-xs"  style="display:none;" onclick="javascript:appConyugeDelete();"><i class="fa fa-trash"></i> Quitar Conyuge</button>
                  <button id="btn_ConyPermiso" type="button" class="btn btn-success btn-xs" style="display:none;" onclick="javascript:appPermisoConyuge();"><i class="fa fa-shield"></i> Solicitar Permiso</button>
                </div>
              </div>
              <div id="div_Conyuge" class="box-body row">
                <input type="hidden" id="hid_ConyPermisoID" value="0">
                <div class="col-md-6">
                  <div class="box-body">
                    <strong><i class="fa fa-user margin-r-5"></i> Basicos</strong>
                    <p class="text-muted">
                      Nombres: <a id="lbl_ConyNombres"></a><br>
                      Apellidos: <a id="lbl_ConyApellidos"></a><br>
                      <span id="lbl_ConyTipoDNI"></span>: <a id="lbl_ConyNroDNI"></a><br>
                      Fecha Nac: <a id="lbl_ConyFechaNac"></a><br>
                      Estado Civil: <a id="lbl_ConyEcivil"></a>
                      <hr style="margin-top:-5px;"/>
                    </p>

                    <strong><i class="fa fa-phone margin-r-5"></i> Contacto</strong>
                    <p class="text-muted">
                      Celular: <a id="lbl_ConyCelular"></a><br>
                      Telefono Fijo: <a id="lbl_ConyTelefijo"></a><br>
                      Correo: <a id="lbl_ConyEmail"></a>
                      <hr style="margin-top:-5px;"/>
                    </p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="box-body">
                    <strong><i class="fa fa-graduation-cap margin-r-5"></i> Profesionales</strong>
                    <p class="text-muted">
                      Grado Instruccion: <a id="lbl_ConyGInstruccion"></a><br>
                      Profesion: <a id="lbl_ConyProfesion"></a><br>
                      Ocupacion: <a id="lbl_ConyOcupacion"></a>
                      <hr style="margin-top:-5px;"/>
                    </p>

                    <strong><i class="fa fa-map-marker margin-r-5"></i> Ubicacion</strong>
                    <p class="text-muted">
                      Direccion: <a id="lbl_ConyDireccion"></a> &raquo; (<a id="lbl_ConyUbicacion"></a>)<br>
                      Referencia: <a id="lbl_ConyReferencia"></a><br>
                      Medidor de Luz: <a id="lbl_ConyMedidorluz"></a><br>
                      Medidor de Agua: <a id="lbl_ConyMedidoragua"></a><br>
                      Tipo de Vivienda: <a id="lbl_ConyTipovivienda"></a>
                      <hr style="margin-top:-5px;"/>
                    </p>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="box-body">
                    <div class="form-cony">
                      <strong><i class="fa fa-heart"></i> Conyugal</strong>
                      <p id="div_ConyTiempo" class="text-muted">
                        Tiempo de Relacion: <a id="lbl_ConyTiempoRel"></a>
                      </p>
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
  <div class="modal fade" id="modalCony" role="dialog"></div>
  <div class="modal fade" id="modalLabo" role="dialog"></div>
  <div class="modal fade" id="modalPers" role="dialog"></div>
</section>

<script src="pages/mtto/padres/script.js"></script>
<script>
  $(document).ready(function(){
    Persona.addModalToParentForm('modalPers');
    Laboral.addModalToParentForm('modalLabo');
    Conyuge.addModalToParentForm('modalCony');
    appPadresReset();
  });
</script>
<?php } ?>