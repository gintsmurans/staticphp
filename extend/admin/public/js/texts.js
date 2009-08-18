
$().ready(function(){

  // Init textarea as wysiwyg
  $('#texts_text').wysiwyg();
  
  // Cache all dom elements into variables
  var title = $('#texts_title');
  var url = $('#texts_url');
  var text = $('#texts_text');
  var active = $('#texts_active');
  
  var get_url_button = $('#texts_get_url_button');
  var add_edit_buttons = $('#texts_add_button, #texts_edit_button');


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

      $.post(AJAX_URL + 'texts/get_url/'+ language +'/'+ menu_id +'/'+ parent_id, {title : title.val()}, function(data){
        show_loader(get_url_button);
        if (data.error)
        {
          show_msg(data.error, 'failed');
          url.val('');
          title.select().focus();
        }
        else if (data.url)
        {
          url.val(data.url);
        }
      }, 'json');
    }
    else
    {
      title.select().focus();
    }
  });
  
  
  add_edit_buttons.bind('click', function(){
    show_msg();

    if ($.trim(title.val()) == '' || $.trim(url.val()) == '' || $.trim(text.val()) == '')
    {
      title.select().focus();
      show_msg('Please, fill all fields', 'failed');
    }
    else
    {
      var data = {
        title : title.val(),
        url : url.val(),
        text : text.val(),
        active : (active.is(':checked') ? 1 : 0)
      };
      
      if (typeof texts_item_id != 'undefined')
      {
        data.item_id = texts_item_id;
      }

      show_loader(add_edit_buttons);

      $.post(AJAX_URL + 'texts/'+ (this.id == 'texts_add_button' ? 'add_item' : 'edit_item') +'/'+ language +'/'+ menu_id +'/'+ parent_id, data, function(data){
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
