jQuery(function($) {
	$('.module_list').change(function(){
		var ischecked = $(this).find(':checkbox').attr('checked');
		if(ischecked) $(this).find('.each_logo, .each_watermark').show();
		else $(this).find('.each_logo, .each_watermark').hide();
	});
});
