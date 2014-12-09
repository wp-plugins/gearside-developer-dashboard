jQuery.noConflict();

jQuery(document).ready(function() {

	//PHG Metabox
	jQuery(document).on("keyup", "input.findterm", function(){
		jQuery("input.findterm").attr("placeholder", "Search files");
	});

	jQuery(document).on("submit", ".searchfiles", function(e){

		if ( jQuery("input.findterm").val().trim().length >= 3 ) {

			jQuery("#searchprogress").removeClass().addClass("fa fa-spinner fa-fw fa-spin");

			jQuery.ajax({
				type: "POST",
				url: bloginfo['admin_ajax'],
				data: {
					action: "search_theme_files",
					data: [{
						"directory": jQuery("select.searchdirectory").val(),
						"searchData": jQuery("input.findterm").val()
					}]
				},
				success: function(response){
					jQuery("#searchprogress").removeClass().addClass("fa fa-search fa-fw");
					jQuery('div.search_results').html(response).addClass('done');
				},
				error: function(MLHttpRequest, textStatus, errorThrown){
					jQuery("div.search_results").html(errorThrown).addClass('done');
				},
				timeout: 60000
			});
		} else {
			jQuery("input.findterm").val("").attr("placeholder", "Minimum 3 characters.");
		}
		e.preventDefault();
		return false;
	});

	jQuery(document).on("click", ".linenumber", function(){
		jQuery(this).parents('.linewrap').find('.precon').slideToggle();
		return false;
	});

	jQuery(document).on("click", ".todo_help_icon", function() {
		jQuery('.todo_help_con').slideToggle();
		return false;
	});


	//Hide TODO files with only hidden items
	jQuery('.todofilewrap').each(function(){
		if ( jQuery(this).find('.linewrap').length == jQuery(this).find('.hidden_todo').length ) {
			jQuery(this).addClass('hidden_file').css('display', 'none');
		}
	});

	jQuery('.togglehiddentodos').on('click', function(){
		jQuery('.hidden_todo, .hidden_file').toggleClass('show-hidden-todos');
		return false;
	});


}); //End Document Ready


jQuery(window).on('load', function() {

	//Window load functions here.

}); //End Window Load