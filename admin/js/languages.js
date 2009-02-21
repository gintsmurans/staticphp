

window.inputs = [];

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
        '<textarea cols="100" rows="20" onblur="save(null, this, \''+id+'\', \''+lang+'\');" onkeyup=" if (event.keyCode == 27){ cancel(this, \''+id+'\', \''+lang+'\'); } ">'+td.innerHTML+'</textarea>'        
    );

    td.onclick = null;
    $(':input:first', td).select();
}
    
function save(data, input, id, lang)
{
    if (data == null && input != null)
    {
        $('#loader').show();
        $.post('<?php echo site_url('language/set', 'ajax'); ?>', {id: id, lang: lang, value: input.value}, function(data){ save(data); }, 'json');
        
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

    for (var k in tr_keys)
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
            $.post('<?php echo site_url('language/delete', 'ajax'); ?>', {id: id}, function(data){ delete_item(null, data); }, 'json');
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