

$().ready(function() {
  window.username = $('#username');
  window.password = $('#password');
  window.submit_button = $('#submit_button');
  window.submit_handler = $('#submit_handler');

  $('.login-form input:first').focus();

  username.add(password).bind('keyup', function(e){
    if (e.keyCode == 13)
    { 
      auth(username.val(), password.val());
    }
    else if (e.keyCode == 27)
    { 
      username.add(password).val('');
      username.focus();
    }
  });

  submit_button.bind('click', function(){
    auth(username.val(), password.val());
  });
});


function auth(username, password)
{
  if ($.trim(username) != '' && $.trim(password) != '')
  {
    show_loader(submit_button);

    $.post(AJAX_URL + 'login/post', {username: username, password: password}, function(data){
      show_loader(submit_button);

      if (data.error)
      {
        show_msg(data.error, 'failed');
        $('#password').val('');
      }
      else if(data.redirect)
      {
        location.href = data.redirect;
      }
    }, 'json');
  }
}
