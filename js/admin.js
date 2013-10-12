;(function($) {
//sending jQuery to an anonymous function so that these functions aren't globally available

	function checkHeadline() {

		//checks if headline exists			
		var titleElement = jQuery( "#title" );
		var title = titleElement.val();

		if ( title.length < 1 ) {
			jQuery( "input[type='checkbox'][name='headlinecheck']").prop('checked', false);

		}	
			else {
				jQuery( "input[type='checkbox'][name='headlinecheck']").prop('checked', true);
			}


		}

	function checkImage() {

		//checks if featured image exists
		if (jQuery("#postimagediv img").length) {
			jQuery( "input[type='checkbox'][name='featuredcheck']").prop('checked', true);
		}	

		else {
			jQuery( "input[type='checkbox'][name='featuredcheck']").prop('checked', false);
		}

	}

	function checkTag() {

		//checks if at least one tag exists
		if (jQuery(".ntdelbutton").length) {
				jQuery( "input[type='checkbox'][name='tagcheck']").prop('checked', true);
			}	

			else {
				jQuery( "input[type='checkbox'][name='tagcheck']").prop('checked', false);
			}
	}

	function checkCategory() {

		//grab the ID of the uncategorized category
		var inputs = jQuery( "#category-all input[type='checkbox']" );
		var uncategorizedid;
		inputs.each( function() {
			var input = $(this);
			var label = input.closest('label').text().toLowerCase().trim();
			if (label == 'uncategorized') {
				uncategorizedid = input.attr('id');
			}
		} )

		//check to see if any categories are checked, ignore the uncategorized category
		if ( jQuery( "#category-all input[type='checkbox']:checked:not(#"+ uncategorizedid + ")" ).length ) {
			jQuery( "input[type='checkbox'][name='catcheck']").prop('checked', true);
		}
			else {
				jQuery( "input[type='checkbox'][name='catcheck']").prop('checked', false);
			}
	}

	function checkExcerpt() {

		//checks if an excerpt exists
		var excerptElement = jQuery( "#excerpt" );
		var excerpt = excerptElement.val();

			if ( excerpt.length < 1 ) {
				jQuery( "input[type='checkbox'][name='excerptcheck']").prop('checked', false);

			}	
				else {
					jQuery( "input[type='checkbox'][name='excerptcheck']").prop('checked', true);
				}

	}

	function hideShowPublish() {

		//hide or shows publish box based on whether all the boxes on the page are checked
		var totalboxes = jQuery(".editorschecklist input[type='checkbox']");
		var checkedboxes = jQuery(".editorschecklist input[type='checkbox']:checked");

		if ( totalboxes.length == checkedboxes.length ) {
				jQuery( "#publish" ).show();
		}

		else {
			jQuery( "#publish" ).hide();
		}
	}


	jQuery ( document ).ready( function() {

		if (jQuery('.editorschecklist').length) {
		// only run these if editorschecklist exists on a post

			//when the page loads, run all of these once
			jQuery( "#publish" ).hide();
			checkHeadline();
			checkImage();
			checkTag();
			checkExcerpt();

			//fire these check functions upon certain actions or intervals
			jQuery( " #title ").keyup( checkHeadline );
			jQuery( "#tagsdiv-post_tag" ).click(checkTag); 
			jQuery( "#categorydiv" ).click(checkCategory);
			jQuery( " #excerpt ").keyup( checkExcerpt );

			
			setInterval(checkImage,1000);
			setInterval(hideShowPublish,500);
		}

	} );

})(jQuery);

