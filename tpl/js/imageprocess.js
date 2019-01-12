jQuery(function($){
var asel = $("#magic_use").find('input:radio:checked').val();
if(asel !='Y') {
	$("#magic_conversion").css('display','none');
	$("#original_format").css('display','none');
	$("#target_format").css('display','none');
}
$("#magic_use").click(function() {
	var sel = $(this).find('input:radio:checked').val();
	if(sel =='Y') {
		$("#magic_conversion").css('display','block');
		$("#original_format").css('display','block');
		$("#target_format").css('display','block');
	}
	else {
		$("#magic_conversion").css('display','none');
		$("#original_format").css('display','none');
		$("#target_format").css('display','none');
	}
});
});

jQuery(function($) {
	var sel = $("#font_type").find('input:radio:checked').val();
	if(sel == 'external') {
        $("#infont").css('display','none');
        $("#outfont").css('display','block');
    } 
    else if(sel == 'internal') {
        $("#outfont").css('display','none');
        $("#infont").css('display','block');
    }
	else {
	$("#outfont").css('display','none');
	$("#infont").css('display','none');
	}


	$("#font_type").click(function() {
	var sel = $(this).find('input:radio:checked').val();
	if(sel == 'external') {
		$("#infont").css('display','none');
		$("#outfont").css('display','block');
	} 
	else {
		$("#outfont").css('display','none');
		$("#infont").css('display','block');
	}
	});
});
jQuery(function($) {
	$('.module_list').each(function(){
                var ischecked = $(this).find(':checkbox').attr('checked');
                if(ischecked) $(this).find('.each_logo').show();
		else $(this).find('.each_logo').hide();
	});
        $('.module_list').change(function(){
                var ischecked = $(this).find(':checkbox').attr('checked');
		
                if(ischecked) $(this).find('.each_logo').show();
                else $(this).find('.each_logo').hide();
        });
});

