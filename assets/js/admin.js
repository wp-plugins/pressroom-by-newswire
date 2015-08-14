var newswire;

(function($){
	
	$('#misc-publishing-actions' ).tooltip();

	/*widget */
	$('.pressroom-widget-admin select').live('change', function(e){
		var $widget = $(e.target).parents('.pressroom-widget-admin');

		$('input[class~="title"]', $widget).val( $(e.target).find('option:selected').text() ) ;
	});
	
	$('.hide-notice').click(function(e){
		var type = $(e.target).attr('type');
		window.location = window.location + '&remove_page_notices';
	});

	var sortable_post_table = jQuery( document.querySelector(".wp-list-table tbody") );
	var fix_helper = function(e, ui) {
		ui.children().children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};

	function apply_toggle_div(selector) {
		$(selector).each(function(index, item){
			$(item).click(function(e){
				//toggle target
				$e = $(e.target).attr('data-toggle');
				$($e).toggle();
			})
		});
	}
	//apply_toggle_div('[data-toggle="toggle"]');
	var toggle_page_styles = function() {

		$('#toggle-pressroom_layout input[type="radio"]').click(function(e){

			if( $(e.target).val() == 'blog') {
				//hide panel
				$('.pressroom_styles').parents('tr').hide();
			} else {
				//show panel
				$('.pressroom_styles').parents('tr').show();
			}
		});


		$('#toggle-newsroom_layout input[type="radio"]').click(function(e){

			if( $(e.target).val() == 'blog') {
				//hide panel
				$('.newsroom_styles').parents('tr').hide();
			} else {
				//show panel
				$('.newsroom_styles').parents('tr').show();
			}
		});


		$('#toggle-pressroom_layout input[type="radio"]:checked').trigger('click');
		$('#toggle-newsroom_layout input[type="radio"]:checked').trigger('click');
	}

	toggle_page_styles();

	function update_sorting_classes(response) {

		jQuery(document.querySelector('.spo-updating-row')).removeClass('spo-updating-row');
		sortable_post_table.removeClass('spo-updating').sortable('enable');

	}

	newswire = window.newswire || {

		/**
		* Init all functions
		*/
		init : function() {
			
			//$('input[type="checkbox"][value="500"]').attr('disabled', true).attr('checked', true);

			if ( typeof typenow!='undefined' &&  typenow == 'pr' ) {

				$('#postexcerpt  h3 > span').html('Abstract');
				$('#postexcerpt .inside p').html('<span class="counter"></span>');
				$('#excerpt').keyup(function(){
					var maxlimit = 196;
					if ($(this).length > maxlimit) {
					 	var str = $(this).val();
				        $(this).val(str.substring(0, maxlimit));
				    }

					$('#postexcerpt .counter').html( $(this).val().length +' of 196 Max characters' );
				});
				$('#excerpt').trigger('keyup');
			}


			if ( sortable_post_table.length && typenow == 'pressroom' && _pressroom_config.newswire_ordering_blocks )
				this.init_sorting();
			
		},
		/**
		* Init sorting of post list
		*/
		init_sorting : function() {
			
			sortable_post_table.sortable({
				items: '> tr',
				cursor: 'move',
				axis: 'y',
				containment: 'table.widefat',
				cancel:	'.inline-edit-row',
				distance: 2,
				opacity: .8,
				helper: fix_helper,
				tolerance: 'pointer',
				start: function(e, ui){
					if ( typeof(inlineEditPost) !== 'undefined' ) {
						inlineEditPost.revert();
					}
					ui.placeholder.height(ui.item.height());
				},
				update: function(event, ui) {
					sortable_post_table.sortable('disable').addClass('spo-updating');
					ui.item.addClass('spo-updating-row');

					var postid = ui.item[0].id.substr(5); // post id

					var prevpostid = false;
					var prevpost = ui.item.prev();
					if ( prevpost.length > 0 ) {
						prevpostid = prevpost.attr('id').substr(5);
					}

					var nextpostid = false;
					var nextpost = ui.item.next();
					if ( nextpost.length > 0 ) {
						nextpostid = nextpost.attr('id').substr(5);
					}

					// go do the sorting stuff via ajax
					jQuery.post( ajaxurl, { 
						action: 'update-menu-order',
						post_type: typenow,
						post_parent: $("#post_ID").val(),
						order: $("#the-list").sortable("serialize"),

					 }, update_sorting_classes );

					// fix cell colors
					var table_rows = document.querySelectorAll('#the-list tr'),
						table_row_count = table_rows.length;
					while( table_row_count-- ) {
						if ( table_row_count%2 == 0 ) {
							jQuery( table_rows[table_row_count]).addClass('alternate');
						} else {
							jQuery( table_rows[table_row_count]).removeClass('alternate');
						}
					}
					// fix quick edit
				}
			});
		} //end init_sorting()
	};

	newswire.init();
	$('#newswire_submission_toggle').click(function(e){
		
		/*
		if ( typeof $(e.target).attr('checked') == 'undefined') {
			//check
			$('#input_disable_submission').val('1');
			$('#nwire-post-meta').hide();
		} else {
			$('#input_disable_submission').val('0');
			$('#nwire-post-meta').show();
		}

		$('#save-post').trigger('click');*/

	});

	$('#newswire_disable_submission').click(function(){
		
		var post_id = $('#post_ID').val();

		$.post( ajaxurl, {
			action: 'newswire_disable_submission',
			id : post_id
		}).done(function(){
			$('#save-post').trigger('click');
			//refresh
			//window.location.href = window.location.href;
		})

	})


	$('#pressroom-toggle-settings input').click(function(e){
		if ( $(e.target).val() == 'standard' && $(e.target).attr('checked')) {
			//show settings
			console.log("checked");

			$('.toggle-settings').parents('tr').show();
		} else {
			console.log('hide');

			$('.toggle-settings').parents('tr').hide();
		}
	});

	$('#pressroom-toggle-settings input:checked').triggerHandler('click');

	//add
	//media upload
	function init_media_uploader(){
		var ed;

		var btn = $('.newswire-media-upload');
		var parentdiv = btn.parent();
		var input =  btn.prev();
		var previewdiv = input.prev();
		var file_frame;	
		var preview = false;		
		
		$('.remove-uploaded-image').click(function(e){
			$(e.target).siblings('input[type=text]').val('').trigger('blur');
			//console.log(e.target);
			//$(e.target).prev().prev().val('');		
			//$(e.target).prev().prev().trigger('blur');

			//$el.prev().prev().val('');
		});

		input.bind('blur',function(e){	
		 	
		 	$(e.target).prev().html('');
		 	
		 	//console.log($(e.target).prev().html());

			if ( $(e.target).val()!= '') {				
				$(e.target).prev().html('<img src="'+ $(e.target).val() +'" border="0" width=300px>');
			}
		});


		btn.click(function(event){
			 //media uploader
			 event.preventDefault();
			
		    // If the media frame already exists, reopen it.
		    /*if ( file_frame ) {
		      file_frame.open();
		      return;
		    } */
			 
		    // Create the media frame.
		    file_frame = wp.media.frames.file_frame = wp.media({
		      title: jQuery( this ).data( 'uploader_title' ),
		      button: {
		        text: jQuery( this ).data( 'uploader_button_text' ),
		      },
		      multiple: false  // Set to true to allow multiple files to be selected
		    });
			 
		    // When an image is selected, run a callback.
		    file_frame.on( 'select', function() 
		    {
		       // We set multiple to false so only get one image from the uploader
		       	attachment = file_frame.state().get('selection').first().toJSON();

		 		if ( console )
		 	   		console.log(attachment);	
		 	   	/*
		 	   	//set image preview
		 	   	if ( typeof attachment.url ) {
		 	   		$('.newswire-image-preview').html('<img src='+attachment.url +'>');
			 		$('#newswire-add-image input[name="newswire_data[img_url]"]').val(attachment.url );
		 	   	} else if ( attachment.sizes &&  attachment.sizes.url ) {
			 		$('.newswire-image-preview').html('<img src='+attachment.sizes.url+'>');
			 		$('#newswire-add-image input[name="newswire_data[img_url]"]').val(attachment.sizes.url);
			 	}
			 		
		 		$('#newswire-add-image input[name="newswire_data[img_caption]"]').val(attachment.caption || attachment.title);
		 		$('#newswire-add-image input[name="newswire_data[img_alt_tag]"]').val(attachment.alt || attachment.title);
		 		$('#newswire-add-image input[name="newswire_data[img_caption_link]"]').val(attachment.link);
		 		$('#newswire-add-image input[name="newswire_data[img_alt_tag_link]"]').val(attachment.title || attachment.caption );


		      // Do something with attachment.id and/or attachment.url here
		      */
		      	$(event.target).prev().val(attachment.url);		      	
		      	$(event.target).prev().triggerHandler('blur');
		    });
			 
			 // Finally, open the modal
			file_frame.open();

		});
	}
	init_media_uploader();

	

	//
	function add_pin_as_link_row() {
		var newrow = '<tr class="pin_as_link_row">' +
						'<td style="width: 3%"><input type="checkbox"></td>' +
						'<td><input type="text" name="newswire_data[text][]" value="" style="width:100%;"></td>' +
						'<td style="width: 5%">&nbsp;</td>' +
						'<td><input type="text" name="newswire_data[link][]"  value="" style="width:100%;"></td>' +
					'</tr>';
		$('#pin_as_links_table ').append(newrow);
		
	}
	$('#pin_as_link_add_row').click(add_pin_as_link_row);

	$('#newswire-validate-api-ajax').click(function(e){
		e.preventDefault();
		$this = $(e.target);
		$this.attr('disabled', true);		
		$this.parent().find('.spinner').show();

		$.post(ajaxurl, {action: 'validate_newswire_api'}).done(function(res){
			
			if ( res == 'valid') {
				
				$('.newswire-error').hide();
				$this.parent().find('.spinner').hide();
				$this.attr('disabled', false);		
				$('.newswire-validate-api-success').remove();
				//add api has been validated
				
				$this.parent().parent().prepend('<div class="newswire-validate-api-success updated success">Newswire account has been validated successfully</div>');
				window.location = window.location;
			} else {
				//res = 'Please contact support!';
				$('.newswire-error').hide();
				$this.parent().find('.spinner').hide();
				$this.attr('disabled', false);		
				$('.newswire-validate-api-success').remove();

				$this.parent().parent().prepend('<div class="newswire-validate-api-success updated error">'+ res +'</div>');
				alert(res);
			   //	window.location = window.location;
			}
		});
	});
	

	//toggle click function for checkobx
	
	$('.category-filter input:checkbox').click(function(e){
		var check = $(e.target).parent().find(':checkbox');
		//console.log(check);
		if ( !check.attr('checked') ) {
			//find chhildren
			check.attr('checked', this.checked);
		}
	});

	$('#toggle-all-categories').click(function(e){
		$('.category-filter input[type="checkbox"]').not(":disabled").attr('checked', this.checked);
	});

	$('.wp-color-picker').wpColorPicker({palettes: true});

	var $embed_pin = $('#placeholder-text-editor-pin-as-embed');
	if ( $embed_pin ) {
		$('#content').blur(function(){
			if ( $('#content').val() == '')
				$('#placeholder-text-editor-pin-as-embed').show();
		});
		$('#content').focus(function(){
			$('#placeholder-text-editor-pin-as-embed').hide();
		})	

		$('#content').trigger('focus');
	}

	var $quote_pin = $('#placeholder-text-editor-pin-as-quote');
	if ( $quote_pin ) {
		$('#content').blur(function(){
			if ( $('#content').val() == '')
				$('#placeholder-text-editor-pin-as-quote').show();
		});
		$('#content').focus(function(){
			$('#placeholder-text-editor-pin-as-quote').hide();
		})	

		$('#content').trigger('focus');
	}
	
	var $social_pin = $('#placeholder-text-editor-pin-as-social');
	if ( $social_pin ) {
		$('#content').blur(function(){
			if ( $('#content').val() == '')
				$('#placeholder-text-editor-pin-as-social').show();
		});
		$('#content').focus(function(){
			$('#placeholder-text-editor-pin-as-social').hide();
		})	

		$('#content').trigger('focus');
	}

})(jQuery);