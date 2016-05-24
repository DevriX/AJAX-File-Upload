/**
  * Did you know? you can modify this by copying the entire plugin folder (ajax-upload-file/)
  * into your child theme, and from there you can edit any file except the core files ( for 
  * core files, any changes made in your child theme won't be applicable )
  *
  * @since 0.1
  * @author Samuel Elh <samelh.com/contact/>
  */

jQuery(document).ready(function($) {

	jQuery(document).on("change", "input[name='afu_file']", function(e) {

		var field = $(this)
		  , container = field.closest(".afu-process-file")
		  , file = field.val()
		  , upload = jQuery( "label.upload", container )
		  , select = jQuery( "label.select", container )
		  , placeholder = jQuery( 'span', select ).text();

	    if( file ) {
	    	upload.removeAttr("disabled");
			jQuery( "span", select ).text( " "+file.replace(/^.*[\\\/]/, '') );
	    } else {
	    	upload.attr("disabled", "disabled");
			jQuery( "span", select ).text( placeholder );
	    }

		e.preventDefault();

	});


	jQuery(document).on("click", ".afu-process-file label.upload", function(e) {

		var upload = $(this)
		  , container = upload.closest(".afu-process-file")
		  , field = jQuery( "input[name='afu_file']", container )
		  , file = field.val()
		  , remove = jQuery( "label.remove", container )
		  , nonce = jQuery( "input[name='_afu_nonce']", container )
		  , file_data = field.prop('files')[0]
	      , form_data = new FormData()
	      , ajaxurl = ajax_file_upload.ajax_path
	      , select = jQuery( "label.select", container )
	      , task = JSON.parse( container.attr("data-task") );

		if( "disabled" === upload.attr("disabled") ) {
			return;
		}

		upload.attr("disabled", "disabled");

	    if( "string" !== typeof ajaxurl ) {
	    	console.error("AJAX file upload error: path to admin ajax not set.");
	    	return;
	    }

		if( nonce.length ) {
			nonce = nonce.val();
		}

		if( ! file ) {
	    	console.error("AJAX file upload error: file not set.");
			return;
	    }

	    form_data.append('file', file_data);
	    form_data.append('action', 'ajax_file_upload');
	    form_data.append('_afu_nonce', nonce);
		if( task.unique_identifier ) {
		    form_data.append('id', task.unique_identifier);
		}

	    if( task.on_success_set_input_value ) {
	    	if( jQuery( task.on_success_set_input_value ).length == 0 ) {
	    		console.error( "AJAX file upload error: Your field \"%s\" to set value was not found.", task.on_success_set_input_value );
	    		task.on_success_set_input_value = false;
	    	} else {
	    		task.on_success_set_input_value = jQuery(task.on_success_set_input_value);
	    	}
	    }

	    if( task.show_preloader ) {
	    	if( jQuery( task.show_preloader ).length == 0 ) {
	    		console.error( "AJAX file upload error: Your preloader \"%s\" was not found.", task.show_preloader );
	    		task.show_preloader = false;
	    	} else {
	    		task.show_preloader = jQuery(task.show_preloader);
	    	}
	    }

	    if( task.set_background_image ) {
	    	if( jQuery( task.set_background_image ).length == 0 ) {
	    		console.error( "AJAX file upload error: Your set-background-image selector \"%s\" was not found.", task.set_background_image );
	    		task.set_background_image = false;
	    	} else {
	    		task.set_background_image = jQuery(task.set_background_image);
	    	}
	    }

	    if( task.set_image_source ) {
	    	if( jQuery( task.set_image_source ).length == 0 ) {
	    		console.error( "AJAX file upload error: Your set-image-source selector \"%s\" was not found.", task.set_image_source );
	    		task.set_image_source = false;
	    	} else {
	    		task.set_image_source = jQuery(task.set_image_source);
	    	}
	    }

	    if( ! task.show_preloader ) {
		    // if no preloader to show
		    jQuery( "i.afuico", select ).attr("data-class", function(){
		    	return $(this).prop("classList")[1];
		    })
		    .attr("class", function(){
		    	return $(this).prop("classList")[0]+' afuico-spin6 animate-spin';
		    })
		    .closest("label").children("span").text(function(){
		    	return task.default_loading_text ? " "+task.default_loading_text : $(this).text();
		    });
		    // end if no preloader to show
		} else {
			// show user preloader
			task.show_preloader.fadeIn();
		}

	    $.ajax({
            url: ajaxurl,
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,                         
            type: 'post',
            success: function( response ){

            	response = JSON.parse(response);

            	ajax_file_upload.create_event( "afu_got_response", {"response": response, "selector": container} );
            	
            	if( response.success ) {

	            	ajax_file_upload.create_event( "afu_file_uploaded", {"response": response, "selector": container} );

	            	if( task.on_success_alert ) {
						alert( task.on_success_alert );
					}

					if( task.on_success_set_input_value ) {
						task.on_success_set_input_value.val(response.media_uri);
					}

					if( task.on_success_dialog_prompt_value ) {
						window.prompt( task.on_success_dialog_prompt_value, response.media_uri );
					}

					if( task.set_background_image ) {
				    	task.set_background_image.css({"background-image": "url('"+response.media_uri+"')"});
				    }

				    if( task.set_image_source ) {
				    	task.set_image_source.attr("data-src", task.set_image_source.attr("src"));
				    	task.set_image_source.attr("src", response.media_uri);				    	
				    }

				    if( task.disallow_reupload ) {
				    	select.removeAttr("for").attr("onclick", "return");
				    	upload.fadeOut(200, function(){
				    		$(this).remove();
				    	});
				    }

				    if ( remove ) {
				    	remove.removeAttr("disabled");
				    }

					select.children("span").text(function(){
				    	return $(this).attr("data-text");
				    });

            	} else {
            	
            		upload.removeAttr("disabled", "disabled");
	            	ajax_file_upload.create_event( "afu_error_uploading", {"response": response, "selector": container} );

	            	if( task.on_fail_alert_error_message && response.error_message ) {
	            		alert(response.error_message);
	            	}

	            	else if( task.on_fail_alert ) {
						alert( task.on_fail_alert );
					}

					if ( remove ) {
				    	remove.attr("disabled", "disabled");
				    }
            	
            	}

			    if( ! task.show_preloader ) {
		        	// if no preloader to show
				    jQuery( "i.afuico", select ).attr("class", function(){
				    	return $(this).prop("className").replace(/afuico-spin6 animate-spin/g, $(this).attr('data-class'));
				    })
				    // end if no preloader to show
			    } else {
					// hide user preloader
					task.show_preloader.fadeOut();
				}

            },
            error: function() {
               	
            	console.error( "Error while uploading file" );
           		upload.removeAttr("disabled", "disabled");
            	ajax_file_upload.create_event( "afu_error_uploading", {"success": false, "error_message": "$.ajax error", "selector": container } );

            	if( ! task.show_preloader ) {
		        	// if no preloader to show
				    jQuery( "i.afuico", select ).attr("class", function(){
				    	return $(this).prop("className").replace(/afuico-spin6 animate-spin/g, $(this).attr('data-class'));
				    })
				    // end if no preloader to show
			    } else {
					// hide user preloader
					task.show_preloader.fadeOut();
				}

				if( task.on_fail_alert ) {
					alert( task.on_fail_alert );
				}

            }
	    });

		e.preventDefault();

	});


	jQuery(document).on("click", ".afu-process-file label.remove", function(e) {

		var remove = $(this)
		  , container = remove.closest(".afu-process-file")
		  , upload = jQuery( "label.upload", container )
		  , field = jQuery( "input[name='afu_file']", container )
		  , file = field.val()
	      , select = jQuery( "label.select", container )
	      , task = JSON.parse( container.attr("data-task") );
	
		if( "disabled" === remove.attr("disabled") ) {
			return;
		}
	    remove.attr("disabled", "disabled");

		ajax_file_upload.create_event( "afu_file_removed", { "this": remove, "container": container } );

		if( task.on_success_set_input_value && jQuery(task.on_success_set_input_value).length ) {
			jQuery(task.on_success_set_input_value).val('');
		}

		if( task.set_background_image && jQuery(task.set_background_image).length ) {
	    	jQuery(task.set_background_image).css({"background-image": ""});
	    }

	    if( task.set_image_source && jQuery(task.set_image_source).length ) {
	    	jQuery(task.set_image_source).attr("src", function(){
	    		return $(this).attr("src");
	    	});
	    }

	    if( task.disallow_reupload ) {
	    	select.removeAttr("for").attr("onclick", "return");
	    	upload.fadeOut(200, function(){
	    		$(this).remove();
	    	});
	    	remove.fadeOut(200, function(){
	    		$(this).remove();
	    	});
	    }

		e.preventDefault();
	
	});

});