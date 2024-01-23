<script src="libs/moment/min/moment.min.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><i class="fa fa-user"></i> <b>Perfil de Usuario</b></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Perfil</li>
  </ol>
</section>
<section class="content">
  <div class="row">
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-body box-profile">
          <img id="perfil_imagen" class="profile-user-img img-responsive img-circle" src="data/personas/fotouser.jpg" alt="Foto de Usuario">
          <h3 id="perfil_nombrecorto" class="profile-username text-center" style="font-family:flexoregular;font-weight:bold;"></h3>
          <p id="perfil_cargo" class="text-muted text-center"></p>
          <ul class="list-group list-group-unbordered">
            <li class="list-group-item">
              <span>DUI</span> <a id="perfil_DNI" class="pull-right"></a></li>
            <li class="list-group-item">
              <span>Celular</span> <a id="perfil_Celular" class="pull-right"></a></li>
            <li class="list-group-item">
              <span>Agencia</span> <a id="perfil_Agencia" class="pull-right"></a></li>
          </ul>
        </div>
      </div>
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title" style="font-family:flexoregular;font-weight:bold;">Acerca de mi</h3>
        </div>
        <div class="box-body">
          <span style="font-family:flexoregular;font-weight:bold;"><i class="fa fa-envelope margin-r-5"></i> Correo</span>
          <p id="perfil_Correo" class="text-muted"></p>
          <hr>
          <span style="font-family:flexoregular;font-weight:bold;"><i class="fa fa-map-marker margin-r-5"></i> Direccion</span>
          <p id="perfil_Direccion" class="text-muted"></p>
        </div>
      </div>
    </div>
    <div class="col-md-9">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#datosPersonal" data-toggle="tab"><i class="fa fa-address-card"></i> Personal</a></li>
          <li><a href="#password" data-toggle="tab"><i class="fa fa-key"></i> Password</a></li>
          <?php //<li><a href="#timeline" data-toggle="tab">Timeline</a></li> ?>
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
                  <b><i class="fa fa-map-marker margin-r-5"></i> Ubicacion</b>
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
                </div>
              </div>
            </div>
          </div>
          <div id="password" class="tab-pane">
            <form class="form-horizontal" autocomplete="off">
              <div class="box-body">
                <div class="col-md-12">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon no-border">Nuevo Password</span>
                      <input type="password" class="form-control" id="txt_passwordNew" placeholder="password..." autocomplete="new-password">
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon no-border">Repetir Password</span>
                      <input type="password" class="form-control" id="txt_passwordRenew" placeholder="repetir password..." autocomplete="new-password">
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-10">
                  <button type="button" class="btn btn-danger" onclick="javascript:appProfileCambiarPassw(<?php echo($_SESSION['usr_ID']); ?>,'#txt_passwordNew','#txt_passwordRenew');">Cambiar Password</button>
                </div>
              </div>
            </form>
          </div>
          <div id="timeline" class="tab-pane">
            <!-- The timeline -->
            <ul class="timeline timeline-inverse">
              <!-- timeline time label -->
              <li class="time-label">
                <span class="bg-blue" style="font-family:flexobold;">10 Feb. 2014</span>
              </li>
              <li>
                <i class="fa fa-plus bg-aqua"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o"></i> 5 mins ago</span>
                  <h3 class="timeline-header no-border"><a href="#">Sarah Young</a> accepted your friend request</h3>
                </div>
              </li>
              <li>
                <i class="fa fa-pencil bg-yellow"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o"></i> 27 mins ago</span>
                  <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>
                </div>
              </li>
              <li>
                <i class="fa fa-minus bg-red"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o"></i> 27 mins ago</span>
                  <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>
                </div>
              </li>
              <li class="time-label">
                <span class="bg-green" style="font-family:flexobold;">3 Jan. 2014</span>
              </li>
              <li>
                <i class="fa fa-user bg-aqua"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o"></i> 5 mins ago</span>
                  <h3 class="timeline-header no-border"><a href="#">Sarah Young</a> accepted your friend request</h3>
                </div>
              </li>
              <li>
                <i class="fa fa-comments bg-yellow"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o"></i> 27 mins ago</span>
                  <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>
                </div>
              </li>
              <li>
                <i class="fa fa-clock-o bg-gray"></i>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script type="text/javascript" src="libs/webtoolkit/webtoolkit.sha1.js"></script>
<script src="pages/global/profile/script.js"></script>
<script>
  $(document).ready(function(){
    appProfile(<?php echo($_SESSION['usr_ID']); ?>);
  });
</script>
