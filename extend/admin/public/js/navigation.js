
$().ready(function(){

  $('.filetree').treeview({
    collapsed : true,
    persist : 'cookie'
  });

  var title = $('#nav_title');
  var url = $('#nav_url');
  var model = $('#nav_model');
  var active = $('#nav_active');

  var get_url_button = $('#nav_get_url_button');
  var add_edit_buttons = $('#nav_add_button, #nav_edit_button');


  title.bind('change', function(){
    if (url.val() == '')
    {
      get_url_button.click();
    }
  });


  get_url_button.bind('click', function(){
    if ($.trim(title.val()) != '')
    {
      show_msg('');
      show_loader(get_url_button);

      $.post(AJAX_URL + 'navigation/get_url/' + language + '/' + menu_id, {title : title.val()}, function(data){
        show_loader(get_url_button);
        if (data.error)
        {
          show_msg(data.error, 'failed')
          url.val('');
          title.select().focus();
        }
        else if (data.url)
        {
          url.val(data.url);
        }
      }, 'json');
    }
  });


  add_edit_buttons.bind('click', function(){
    show_msg('');

    if ($.trim(title.val()) == '' || $.trim(url.val()) == '' || $.trim(model.val()) == '')
    {
      title.select().focus();
      show_msg('Please, fill all fields', 'failed');
    }
    else
    {
      var data = {
        title : title.val(),
        url : url.val(),
        model : model.val(),
        active : (active.is(':checked') ? 1 : 0)
      };
      
      show_loader(add_edit_buttons);

      $.post(AJAX_URL + 'navigation/' + (this.id == 'nav_add_button' ? 'add_item' : 'edit_item') +'/'+ language +'/'+ menu_id +'/'+ parent_id, data, function(data){
        if (data.error)
        {
          show_loader(add_edit_buttons);
          show_msg(data.error, 'failed');
        }
        else if (data.redirect)
        {
          location.href = data.redirect;
        }
      }, 'json');
    }
  });
});
