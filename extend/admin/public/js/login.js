

$().ready(function(){
  
    $('.login-form input:first').focus();
    
    $('#password, #username').bind('keyup', function(e){
      if (e.keyCode == 13)
      { 
        auth($('#username').val(), $('#password').val()); 
      }
      else if (e.keyCode == 27)
      { 
        $('#password, #username').val('');
      }
    }); 
});


function auth(username, password, data)
{
  if (data == null)
  {
    if ($.trim(username) != '' && $.trim(password) != '')
    {
      $('#loader').show();
      $.post(AJAX_URL + 'login/post', {username: username, password: password}, function(data){ auth(null, null, data); }, 'json');
    }
  }
  else
  {
      $('#loader').hide();
      if (data.error)
      {
          $('#error').html(data.error);
          $('#password').val('');
      }
      else if(data.redirect)
      {
          location.href = data.redirect;
      }
  }
}
