//=========================funciones para boton Submit============================
function appSubmitButton(miTarea,miModulo){
  switch (miTarea) {
    case "logout": location.href = 'includes/sess_logout.php'; break;
    default: location.href = 'interfaz.php?page='+miTarea; break;
  }
}

function appNotificacionesSetInterval(){
  $.ajax({
    url:'includes/sql_select.php',
    type:'POST',
    dataType:'json',
    data:{"appSQL":JSON.stringify({TipoQuery:'notificaciones'})},
    success: function(data){ appNotificacionesData(data); },
    complete:function(data){ setTimeout(appNotificacionesSetInterval,4000); }
  });
}

function appNotificaciones(){
  appAjaxSelect('includes/sql_select.php',{TipoQuery:'notificaciones'}).done(function(data){
    appNotificacionesData(data);
  });
}

function appNotificacionesData(resp){
  //cuenta total
  $('.NotifiCount').html(resp.cuenta);
  if(resp.cuenta>0) { $('#lblNotifiCount1').show(); } else { $('#lblNotifiCount1').hide(); }

  //detalle de datos
  if(resp.tabla.length>0){
    let appData = "";
    $.each(resp.tabla,function(key, valor){
      if(key<=4) { appData += '<li><a href="javascript:appSubmitButton(\'notificaciones\');" title="'+(valor.usr_solic)+' &raquo; '+(valor.persona)+'"><i class="fa fa-shield text-aqua"></i> '+(valor.usr_solic)+' <i class="fa fa-angle-double-right" style="width:12px;"></i>'+(valor.persona)+'</a></li>'; }
      else { return false; }
    });
    $('#appInterfazNotificaciones').html(appData);
  } else{
    $('#appInterfazNotificaciones').html("");
  }
}

function inicioAPP(){
  //const user = JSON.parse(localStorage.getItem("user"));
  appFetch({ TipoQuery:'selDataUser' },"includes/sess_interfaz.php").then(resp => {
    resp.menu = JSON.parse(resp.menu);
    //console.log(resp.menu);
    
    
    /*if(resp.rolID>102){ //oncontextmenu="return false;" ondragstart="return false;" onselectstart="return false;"
      $(document.body).attr("oncontextmenu","return false;");
      $(document.body).attr("onselectstart","return false;");
      $(document.body).attr("ondragstart","return false;");
    }*/
    document.querySelector("#ifaz_menu_imagen").src = (resp.urlfoto);
    document.querySelector("#ifaz_menu_nombrecorto").innerHTML = (resp.nombrecorto);
    document.querySelector("#ifaz_menu_login").innerHTML = (resp.login);
    document.querySelector("#ifaz_barra_imagen").src = (resp.urlfoto);
    document.querySelector("#ifaz_barra_nombrecorto").innerHTML = (resp.nombrecorto);
    document.querySelector("#ifaz_perfil_imagen").src = (resp.urlfoto);
    document.querySelector("#ifaz_perfil_nombrecorto").innerHTML = (resp.nombrecorto);
    document.querySelector("#ifaz_perfil_cargo").innerHTML = (resp.cargo);
  });
}