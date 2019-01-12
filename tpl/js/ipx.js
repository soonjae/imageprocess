jQuery(function($){
$(".fontname").hide();
$(".fontname_toggle").toggle(function(){
    $(".fontname").show(500);},
    function(){
    $(".fontname").hide();}
);
});
jQuery(function($){
$('#colorpick1, #colorpick2').ColorPicker({
    onSubmit: function(hsb, hex, rgb, el) {
        $(el).val(hex);
        $(el).ColorPickerHide();
    },
    onBeforeShow: function () {
        $(this).ColorPickerSetColor(this.value);
    }
}).bind('keyup', function(){
    $(this).ColorPickerSetColor(this.value);
});
});
jQuery(function($) {
	$('.module_list').change(function(){
		var ischecked = $(this).find(':checkbox').attr('checked');
		if(ischecked) $(this).find('.each_logo').show();
		else $(this).find('.each_logo').hide();
	});
});