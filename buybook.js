jQuery.noConflict();
jQuery(document).ready(function(){
	jQuery(".toggle").each(function(){
		jQuery(this).find(".box-invisible").hide();
		});
		jQuery(".toggle").each(function(){
		jQuery(this).find(".trigger").click(function() {
		jQuery(this).toggleClass("active").next().stop(true, true).slideToggle("normal");
		return false;
		});
	});
});
