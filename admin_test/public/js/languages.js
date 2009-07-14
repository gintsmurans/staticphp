
var inputs = [];


$().ready(function(){

  // Add language
  $('#add_language_button').bind('click', function(){
    var add = $('#add_language');
    if ($.trim(add.val()) == '')
    {
      add.focus();
    }
    else
    {
      show_loader('#add_language_button');
      $.post(AJAX_URL + 'languages/add_language', {lang: add.val()}, function(data){
        window.location.reload();
      });
    }
  });
  
  $('#add_language').bind('keyup', function(e){
    if (e.keyCode == 13)
    {
      $('#add_language_button').click();
    }
  });
  
  
  // Copy to web
  $('#copy_to_web').bind('click', function(){
    if (confirm('Are you sure want to copy all languages to website?'))
    {
      window.location.href = BASE_URL + 'languages/copy_to_web';
    }
  });
  

  // Add item
  // Add language
  $('#add_item_button').bind('click', function(){
    var add = $('#add_item');
    if ($.trim(add.val()) == '')
    {
      add.focus();
    }
    else
    {
      var button = show_loader('#add_item_button');
      $.post(AJAX_URL + 'languages/add_item', {ident: add.val()}, function(data){
        $('#add_item_button').html(button);
        if (data.error)
        {
          add.select().focus();
          $('#msg_failed').html(data.error);
          setTimeout("$('#msg_failed').html('');", 3000);
        }
        else if (data.ident)
        {
          add.val('');

          html = '<tr>';
          html += '<td class="hover" onclick="if (confirm(\'Are you sure want to delete this item?\')){ delete_item(\''+ data.ident +'\'); }"><img src="'+ BASE_URL +'css/images/delete.png" alt="" /></td>';
          html += '<td class="hover" onclick="change(this, \''+ data.ident +'\', \'scope\');"></td>';
          html += '<td class="hover" onclick="change(this, \''+ data.ident +'\', \'ident\');">'+ data.ident +'</td>';
      
          for (var k in languages)
          {
              html += '<td class="hover" onclick="change(this, \''+ data.ident +'\', \''+ languages[k] +'\');"></td>';
          }
          
          html += '</tr>';
          
          $('#insert').before(html);
        }
      }, 'json');
    }
  });
  
  $('#add_item').bind('keyup', function(e){
    if (e.keyCode == 13)
    {
      $('#add_item_button').click();
    }
  });  
});


function change(td, id, lang)
{
    // Calculate length
    var length = td.innerHTML.length * 0.8;
    if (length > 100)
    {
        length = 100;
    }
    else if (length < 2)
    {
        length = 3;
    }

    // Save original value
    inputs[id] = td.innerHTML;

    // Change to input
    td.innerHTML = (
        lang == 'scope' || lang == 'ident' ? 
        '<input type="text" value="'+td.innerHTML+'" onblur="save(null, this, \''+id+'\', \''+lang+'\');" onkeyup=" if (event.keyCode == 27){ cancel(this, \''+id+'\', \''+lang+'\'); }else if(event.keyCode === 13){ this.blur(); } " />' : 
        '<textarea cols="100" rows="20">'+td.innerHTML+'</textarea><div><span class="aslink" onclick="save(null, this, \''+id+'\', \''+lang+'\');">Save</span> <span class="aslink" onclick="cancel(this, \''+id+'\', \''+lang+'\');">Cancel</span></div>'
    );

    td.onclick = null;
    $(':input:first', td).select();
    if (lang != 'scope' && lang != 'ident')
    {
      $(':input:first', td).wysiwyg();
      $('#iframeID').focus();
    }
}
    
function save(data, input, id, lang)
{
    if (data == null && input != null)
    {
        $('#loader').show();
        $.post(AJAX_URL + 'language/set', {id: id, lang: lang, value: encodeURIComponent(input.value)}, function(data){ save(data); }, 'json');
        
        var td = input.parentNode;
        td.innerHTML = input.value;
        eval("td.onclick = function(){change(this, '"+id+"', '"+lang+"');}");
    }
    else
    {
        $('#loader').hide();
        if (data.id != null)
        {
            location.reload();
        }
    }
}


function cancel(input, id, lang)
{
    if (inputs[id])
    {
        var td = input.parentNode;
        td.innerHTML = inputs[id];
        eval("td.onclick = function(){change(this, '"+id+"', '"+lang+"');}");
    }
}


function insert_line()
{
    html = '<tr><td></td>';
    
    html += '<td class="hover" onclick="change(this, \'new\', \'scope\');"></td>';

    for (var k in languages)
    {
        html += '<td class="hover" onclick="change(this, \'new\', \''+ tr_keys[k] +'\');"></td>';
    }
    
    html += '</tr>';
    
    $('#insert').before(html);
}


function delete_item(id, data)
{
    if (data == null)
    {
        if (id != null)
        {
            $('#loader').show();
            $.post(AJAX_URL + 'language/delete', {id: id}, function(data){ delete_item(null, data); }, 'json');
        }
    }
    else
    {
        $('#loader').hide();

        if (data.id != null)
        {
            $('#item-'+data.id).
                css('background', '#faf189').
                fadeOut('slow', function(){$('#item-'+data.id).remove();});
        }
    }
}
