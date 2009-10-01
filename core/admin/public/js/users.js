
$().ready(function(){
	$('#access_list input').bind('change', function(){
		var not = false;
		$(this).parent('li').find('ul input').attr('checked', this.checked);
		$(this).parent('li').parent('ul').find('input').each(function() {
			if ($(this).is(':not(:checked)'))
			{
				$(this).parents('li').find('input:first').attr('checked', '');
				not = true;
				return;
			}
		});
		
		if (not == false)
		{
			var parent_checkbox = $(this).parents('li').find('input:first');

			parent_checkbox.parent('li').find('ul').each(function(){
				not = false;
				$(this).find('input').each(function(){
					if (!this.checked)
					{
						not = true;
					}
				});
				
				if (not == false)
				{
					$(this).parent('li:first').find('input:first').attr('checked', 'checked');
				}
			});
		}
	});
});
