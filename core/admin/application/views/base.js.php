
// Base urls
var BASE_URL = '<?php echo base_url(); ?>';
var AJAX_URL = '<?php echo site_url('', 'ajax'); ?>';

// Loader pre-loader
var loader = document.createElement('img');
loader.src = BASE_URL + 'css/images/loader.gif';
loader.id = 'loader';

// Show loader
function show_loader(element)
{
  element = $(element);
  if (element.is(':visible'))
  {
    element.hide(0);
    $(element).after(loader);
  }
  else
  {
    $('#loader').remove();
    element.show(0);
  }
}

// Show msg Failed
function show_msg(message, type)
{
  if (message === '')
  {
    dMsg.removeClass().html('&nbsp;');
  }
  else
  {
    dMsg.addClass('msg_' + type).html(message);
  }
}

// Onload
$().ready(function(){
  window.dMsg = $('#msg');
});
