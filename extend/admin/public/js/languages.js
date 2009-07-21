
var tmp_input;
var current_td;
var current_ident;
var current_field;


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
  
  // Copy from web
  $('#copy_from_web').bind('click', function(){
    if (confirm('Are you sure want to copy all languages from website?'))
    {
      window.location.href = BASE_URL + 'languages/copy_from_web';
    }
  });
  
  // Copy scope to web
  $('#copy_scope_to_web').bind('click', function(){
    scope_change = $('#scope_change');
    if (scope_change.val() != 0 && confirm('Are you sure want to copy this scope to website?'))
    {
      location.href = BASE_URL +'languages/copy_scope_to_web/'+ scope_change.val();
    }
  });
  
  // Copy scope from web
  $('#copy_scope_from_web').bind('click', function(){
    scope_change = $('#scope_change');
    if (scope_change.val() != 0 && confirm('Are you sure want to copy this scope from website?'))
    {
      location.href = BASE_URL +'languages/copy_scope_from_web/'+ scope_change.val();
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
      $.post(AJAX_URL + 'languages/add_item', {ident: add.val(), scope: (current_scope ? current_scope : '')}, function(data){
        $('#add_item_button').html(button);
        if (data.error)
        {
          alert(data.error);
          add.select().focus();
        }
        else if (data.ident)
        {
          add.val('');

          html = '<tr>';
          html += '<td class="hover delete" onclick="if (confirm(\'Are you sure want to delete this item?\')){ delete_item(\''+ data.ident +'\'); }"><img src="'+ BASE_URL +'css/images/delete.png" alt="" /></td>';
          html += '<td class="hover" onclick="change(this, \''+ data.ident +'\', \'scope\');">'+ data.scope +'</td>';
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
  
  
  // Change scope
  $('#scope_change').bind('change', function(){
    location.href = BASE_URL +'languages/index/'+ this.value;
  });
  
  
  // Document keyup
  $(document).bind('keyup', function(e){
    if (current_td)
    {
      if (e.keyCode == 27)
      {
        cancel();
      }
      else if (e.keyCode == 13)
      {
        save();
      }
    }
  });
});


function change(td, ident, field)
{
  // get JQuery object
  current_td = $(td);
  current_ident = ident;
  current_field = field;

  
  // Unbind td click
  current_td.unbind()[0].onclick = '';


  // Save original value
  tmp_input = current_td.html();


  // Change to input
  if (current_field == 'scope' || current_field == 'ident')
  {
    current_td.html(
      '<input type="text" id="edit-'+ current_ident +'" value="'+ tmp_input +'" />'+
      '&nbsp;&nbsp;<span class="hover" onmouseup="save();"><img src="'+ BASE_URL +'css/images/save.png" /></span>'+
      '&nbsp;<span class="hover" onmouseup="cancel();"><img src="'+ BASE_URL +'css/images/trash.png" /></span>'
    );
    $(':input:first', current_td).select();
  }
  else
  {
    html = '<div class="edit-absolute">';
    html += '<div class="edit-absolute-inner">';
    html += '<textarea id="edit-'+ current_ident +'" cols="100" rows="20">'+ tmp_input +'</textarea>';
    html += '<div class="save_cancel"><span class="hover" onclick="save();"><img src="'+ BASE_URL +'css/images/save.png" /></span> <span class="hover" onclick="cancel();"><img src="'+ BASE_URL +'css/images/trash.png" /></span>';
    html += '</div>';
    html += '</div>';

    html = $(html).css({width: $(window).width(), height: $(document).height()}).appendTo('body').find('.edit-absolute-inner');
    html.css({left: ($(window).width() / 2 - 400), top: $(window).scrollTop() + ($(window).height() / 2 - 175 - 40)});

    $('#edit-'+ current_ident).wysiwyg({
      controls: {
        strikeThrough : { visible : true }, 
        underline : { visible : true },

        separator00 : { visible : true },

        justifyLeft : { visible : true },
        justifyCenter : { visible : true },
        justifyRight : { visible : true },
        justifyFull : { visible : true },
        
        separator01 : { visible : true },
        
        subscript   : { visible : true },
        superscript : { visible : true },

        separator03 : { visible : true },

        undo : { visible : true },
        redo : { visible : true },

        separator04 : { visible : true, separator : true },

        insertOrderedList    : { visible : true },
        insertUnorderedList  : { visible : true },
        insertHorizontalRule : { visible : true },
        
        insertImage : { visible : false },
        
        h1mozilla : { visible : false },
        h2mozilla : { visible : false },
        h3mozilla : { visible : false },

        h1 : { visible : false },
        h2 : { visible : false },
        h3 : { visible : false },
        
        separator08 : { separator : false },
        separator09 : { separator : false },

        increaseFontSize : { visible : false },
        decreaseFontSize : { visible : false },
      }
    });
  }
}
    
function save()
{
  var edit = $('#edit-'+ current_ident);
  if (current_td && edit.length == 1)
  {
    if (tmp_input == edit.val())
    {
      cancel();
    }
    else
    {
      // var td = $(input.parentNode);
      show_loader(current_td);
  
      $.post(AJAX_URL + 'languages/edit_item', {ident: current_ident, lang: current_field, value: encodeURIComponent(edit.val())}, function(data){
        tmp_input = unescape(data.value);
        cancel();
      }, 'json');
    }
  }
}


function cancel()
{
  if (current_td)
  {
    current_td.html(tmp_input);
    eval("current_td.bind('click', function(){ change(this, '"+ current_ident +"', '"+ current_field +"'); });");

    tmp_input = null;
    current_td = null;
    
    if (current_field != 'scope' && current_field != 'ident')
    {
      $('.edit-absolute').remove();
    }
  }
}


function delete_item(ident)
{
  if ($.trim(ident) != '')
  {
    show_loader('#item-'+ ident +' .delete');
    $.post(AJAX_URL + 'languages/delete_item', {ident: ident}, function(data){
        if (data.ident)
        {
          $('#item-'+data.ident).
          css('background', '#faf189').
          fadeOut('slow', function(){$('#item-'+data.ident).remove();});
        }
    }, 'json');
  }
}
