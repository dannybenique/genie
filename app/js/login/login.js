$(document).on('submit','#frmLogin',function(event){
  event.preventDefault();

  let datos = {
    login : document.querySelector('#txt_UserName').value,
    passw : SHA1(document.querySelector("#txt_UserPass").value).toString().toUpperCase()
  }

  $.ajax({
    url:'includes/sess_login.php',
    type:'POST',
    dataType:'json',
    data:{"frmLogin":JSON.stringify(datos)},
    beforeSend:function() {
      $('#botonOK').val('Validando....');
    }
  })
  .done(function(resp){
    console.log(resp);
    if (resp.error===0) { //sin errores
      location.href = 'interfaz.php';
    } else {
      $('.login_WarningText').fadeIn('fast');
      setTimeout(function() {
        $('.login_WarningText').fadeOut('fast');
        document.querySelector('#txt_UserName').value="";
        document.querySelector('#txt_UserPass').value="";
        $('#txt_UserName').focus();
        document.querySelector('#botonOK').value="ACCESAR";
      },2000);
    }
  })
  .fail(function(resp){
    // console.log("fail:.... "+resp);
    $('#pn_Warning').slideDown('fast');
    setTimeout(function() { $('#pn_Warning').slideUp('fast'); },2000);
    document.querySelector('#botonOK').value="ACCESAR";
  });
});
