jQuery(function($){
$(".fontname").hide();
$(".fontname_toggle").toggle(function(){
    $(".fontname").show(500);},
    function(){
    $(".fontname").hide();}
);
});

jQuery(function($) {
	$('.module_list').change(function(){
		var ischecked = $(this).find(':checkbox').attr('checked');
		if(ischecked) $(this).find('.each_logo').show();
		else $(this).find('.each_logo').hide();
	});
});
