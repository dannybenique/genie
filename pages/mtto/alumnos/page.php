<?php if(isset($menu->mtto->submenu->alumnos)){?>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="libs/moment/min/moment.min.js"></script>

<!-- modal de Personas y Laborales y Conyuge -->
<script src="pages/modals/personas/mod.persona.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-gear"></i> <b>Alumnos</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Alumnos</li>
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
              <button type="button" id="btn_DEL" class="btn btn-default btn-sm" onclick="javascript:appAlumnosBotonBorrar();"><i class="fa fa-trash"></i></button>
              <button type="button" id="btn_NEW" class="btn btn-default btn-sm" onclick="javascript:appAlumnosBotonNuevo();"><i class="fa fa-plus"></i></button>
            </div>
            <button type="button" class="btn btn-default btn-sm" onclick="javascript:appAlumnosReset();"><i class="fa fa-refresh"></i></button>
            <div class="btn-group">
              <input type="text" id="txtBuscar" name="txtBuscar" class="form-control input-sm pull-right" placeholder="DNI, persona..." onkeypress="javascript:appAlumnosBuscar(event);" style="text-transform:uppercase;" autocomplete="off">
              <span class="fa fa-search form-control-feedback"></span>
            </div>
            <span id="grdCount" style="display:inline-block;margin-left:5px;font-size:20px;font-weight:600;"></span>
            <a class="pull-right" href="javascript:appAlumnosBotonViewAll();" title="ver todos los registros"><i id="icoViewAll" class="fa fa-toggle-off"></i><input type="hidden" id="hidViewAll" value="0"></a>
          </div>
          <div class="box-body table-responsive no-padding">
            <table class="table table-hover" id="grd">
              <thead>
                <tr>
                  <th style="width:25px;"><input type="checkbox" id="chk_All" onclick="toggleAll(this,'chk_Borrar');" /></th>
                  <th style="width:80px;">Codigo</th>
                  <th style="width:90px;text-align:center;" title="Fecha de Ingreso">Ingreso</th>
                  <th style="width:110px;" title="Documento Unico de Identidad = DNI">DNI</th>
                  <th>Alumnos <i class="fa fa-sort"></i></th>
                  <th>Direccion</th>
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
            <button id="btnCancel" type="button" class="btn btn-default" onclick="javascript:appAlumnosBotonCancel();"><i class="fa fa-angle-double-left"></i> Regresar</button>
            <button id="btnInsert" type="button" class="btn btn-primary pull-right" onclick="javascript:appAlumnosBotonInsert();"><i class="fa fa-save"></i> Guardar</button>
            <button id="btnUpdate" type="button" class="btn btn-info pull-right" onclick="javascript:appAlumnosBotonUpdate();"><i class="fa fa-save"></i> Actualizar</button>
          </div>
          <div id="div_Estado" class="box-body" style="padding-top:0;">
          </div>
        </div>
      </div>
      <div class="col-md-9">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
            <li  class="active"><a href="#datosAlumno" data-toggle="tab"><i class="fa fa-briefcase"></i> Alumno</a></li>
            <li><a href="#datosPersonal" data-toggle="tab"><i class="fa fa-address-card"></i> Personal</a></li>
          </ul>
          <div class="tab-content">
            <div id="datosAlumno" class="tab-pane active">
              <div class="box-body">
                <div class="box-body">
                  <div class="box-body">
                    <div class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:85px;background:#eeeeee;"><b>Ingreso</b></span>
                        <input id="txt_AlumnoFechaIng" type="text" class="form-control" style="width:105px;" disabled="disabled" />
                      </div>
                    </div>
                    <div class="form-group" style="margin-bottom:5px;">
                      <div class="input-group">
                        <span class="input-group-addon" style="width:85px;background:#eeeeee;"><b>Codigo</b></span>
                        <input id="txt_AlumnoCodigo" type="text" class="form-control" placeholder="Codigo..." maxlength="7" disabled="disabled" style="width:150px;"/>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="box-body">
                <ul class="todo-list">
                  <li>
                    <table style="width:100%;">
                      <tr><td style="width:90px;" class="no-padding">
                            <div class="pull-left" style="width:100%;">
                              <a href="javascript:appFamiPadreAdd();" class="btn btn-app" style="margin:0;"><i class="fa fa-edit"></i> Padre</a>
                            </div>
                        </td>
                        <td>
                          <div style="line-height:1;">
                            <a href="javascript:appLinkFamiliar(1);" id="lbl_alumnoFamiPadreNombre" style="font-weight:bold;font-size:15px;"></a><br>
                            <span id="lbl_alumnoFamiPadreDNI" style="font-size:11px;"></span><br/>
                            <span id="lbl_alumnoFamiPadreDireccion" style="font-size:11px;"></span>
                          </div>
                        </td>
                        <td style="width:30px;">
                          <div id="appFamiPadre_botonDel" class="pull-left" style="width:100%;"></div>
                        </td>
                      </tr>
                    </table>
                  </li>
                  <li>
                    <table style="width:100%;">
                      <tr><td style="width:90px;" class="no-padding">
                            <div class="pull-left" style="width:100%;">
                              <a href="javascript:appFamiMadreAdd();" class="btn btn-app" style="margin:0;"><i class="fa fa-edit"></i> Madre</a>
                            </div>
                        </td>
                        <td>
                          <div style="line-height:1;">
                            <a href="javascript:appLinkFamiliar(2);" id="lbl_alumnoFamiMadreNombre" style="font-weight:bold;font-size:15px;"></a><br>
                            <span id="lbl_alumnoFamiMadreDNI" style="font-size:11px;"></span><br/>
                            <span id="lbl_alumnoFamiMadreDireccion" style="font-size:11px;"></span>
                          </div>
                        </td>
                        <td style="width:30px;">
                          <div id="appFamiMadre_botonDel" class="pull-left" style="width:100%;"></div>
                        </td>
                      </tr>
                    </table>
                  </li>
                  <li>
                    <table style="width:100%;">
                      <tr><td style="width:90px;" class="no-padding">
                            <div class="pull-left" style="width:100%;">
                              <a href="javascript:appFamiApoderaAdd();" class="btn btn-app" style="margin:0;">
                                <i class="fa fa-edit"></i> Apoderado
                              </a>
                            </div>
                        </td>
                        <td>
                          <div style="line-height:1;">
                            <a href="javascript:appLinkFamiliar(3);" id="lbl_alumnoFamiApoderaNombre" style="font-weight:bold;font-size:15px;"></a><br>
                            <span id="lbl_alumnoFamiApoderaDNI" style="font-size:11px;"></span><br/>
                            <span id="lbl_alumnoFamiApoderaDireccion" style="font-size:11px;"></span>
                          </div>
                        </td>
                        <td style="width:30px;">
                          <div id="appFamiApodera_botonDel" class="pull-left" style="width:100%;"></div>
                        </td>
                      </tr>
                    </table>
                  </li>
                </ul>
                <div class="box-body">
                  <div id="div_AlumnoAuditoria">
                    <strong><i class="fa fa-eye margin-r-5"></i> Auditoria</strong>
                    <div style="font:10px flexolight;color:gray;">
                      Fecha: <span id="lbl_AlumnoSysFecha"></span><br>
                      Modif. por: <span id="lbl_AlumnoSysUser"></span>
                    </div>
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
                    </div><br>
                    <button id="btn_PersUpdate" type="button" class="btn btn-primary btn-xs" onclick="javascript:appPersonaEditar();"><i class="fa fa-flash"></i> Modificar Datos</button>
                    <button id="btn_PersPermiso" type="button" class="btn btn-success btn-xs" style="display:none;" onclick="javascript:appPermisoPersonas();"><i class="fa fa-shield"></i> Solicitar Permiso</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
  <div class="modal fade" id="modalPers" role="dialog">
  </div>
  <div class="modal fade" id="modalFamiliar" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 id="lbl_modfamTitulo" class="modal-title" style="font-family:flexoregular;font-weight:bold;">Datos Personales de Familiar</h4>
        </div>
        <div class="modal-body">
          <div class="box-body row">
            <div class="col-md-5">
              <div class="box-body">
                <strong><i class="fa fa-address-card margin-r-5"></i> Basicos</strong>
                <p class="text-muted">
                  Nombres: <a id="lbl_modfamNombres"></a><br>
                  Apellidos: <a id="lbl_modfamApellidos"></a><br></span><br>
                  DNI: <a id="lbl_modfamNroDNI"></a><br>
                  Pais Nac: <a id="lbl_modfamPaisNac"></a><br>
                  Lugar Nac: <a id="lbl_modfamLugarNac"></a><br>
                  Fecha Nac: <a id="lbl_modfamFechaNac"></a><br>
                  Edad: <a id="lbl_modfamEdad"></a><br>
                  Sexo: <a id="lbl_modfamSexo"></a><br>
                  Estado Civil: <a id="lbl_modfamEcivil"></a>
                </p>
                <hr/>

                <strong><i class="fa fa-phone margin-r-5"></i> Contacto</strong>
                <p class="text-muted">
                  Celular: <a id="lbl_modfamCelular"></a><br>
                  Telefono Fijo: <a id="lbl_modfamTelefijo"></a><br>
                  Correo: <a id="lbl_modfamEmail"></a><br>
                </p>
                <hr/>

                <strong><i class="fa fa-graduation-cap margin-r-5"></i> Profesionales</strong>
                <p class="text-muted">
                  Grado Instruccion: <a id="lbl_modfamGInstruccion"></a><br>
                  Profesion: <a id="lbl_modfamProfesion"></a><br>
                  Ocupacion: <a id="lbl_modfamOcupacion"></a>
                </p>
              </div>
            </div>
            <div class="col-md-7">
              <div class="box-body">
                <strong><i class="fa fa-map-marker margin-r-5"></i> Ubicacion</strong>
                <table class="table-responsive no-padding">
                  <tr>
                    <td style="width:65px;vertical-align:bottom;">Direccion:</td>
                    <td><a id="lbl_modfamUbicacion" style="font:12px configcondensed_light;"></a><br>
                        <a id="lbl_modfamDireccion"></a></td>
                  </tr>
                </table>
                Referencia: <a id="lbl_modfamReferencia"></a><br>
                Medidor de Luz: <a id="lbl_modfamMedidorluz"></a><br>
                Medidor de Agua: <a id="lbl_modfamMedidorAgua"></a><br>
                Tipo de Vivienda: <a id="lbl_modfamTipovivienda"></a>
                <hr/>

                <strong><i class="fa fa-book margin-r-5"></i> Observaciones</strong>
                <p class="text-muted">
                  <span id="lbl_modfamObservac"></span>
                </p><br><br>
                <div id="div_PersAuditoria">
                  <strong><i class="fa fa-eye margin-r-5"></i> Auditoria</strong>
                  <div style="font:10px flexolight;color:gray;">
                    Fecha: <span id="lbl_modfamSysFecha"></span><br>
                    Modif. por: <span id="lbl_modfamSysUser"></span>
                  </div>
                </div><br>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="background:#f9f9f9;padding:8px;">
          <button type="button" class="btn btn-default pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="pages/mtto/alumnos/script.js"></script>
<script>
  $(document).ready(function(){
    Persona.addModalToParentForm('modalPers');
    appAlumnosReset();
  });
</script>
<?php } ?>