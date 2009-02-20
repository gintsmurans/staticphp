

$().ready(function(){
    $('#auth').focus();
});


function auth(input, data)
{
    if (data == null)
    {
        $('#loader').show();
        $.post('<?php echo site_url('language/auth', 'ajax'); ?>', {p: input.value}, function(data){ auth(null, data); }, 'json');
    }
    else
    {
        $('#loader').hide();
        if (data.error)
        {
            alert(data.error);
        }
        else if(data.done == true)
        {
            location.reload();
        }
    }
}
