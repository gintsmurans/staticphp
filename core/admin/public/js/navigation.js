
$().ready(function(){

  $('.filetree').treeview({
    collapsed : true,
    persist : 'cookie'
  });

  var title = $('#nav_title');
  var url = $('#nav_url');
  var model = $('#nav_model');

  var nav_settings = $('#nav_settings');
  var get_url_button = $('#nav_get_url_button');


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
  
  
  model.bind('change', function() {
    nav_settings.html('');
    if (this.value != '')
    {
      $.post(AJAX_URL + 'navigation/nav_settings/' + language + '/' + menu_id + (parent_id ? '/'+ parent_id : ''), {model : this.value}, function(data) {
        nav_settings.html(data);
      });
    }
  });
});
