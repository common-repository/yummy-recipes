jQuery(function($){
	var inputElementId = $('input[name="yummy_term_custom_image_id"]');
	var previewImageElementId = 'yummy-term-meta-preview-image';
	var previewImageElement = $('#'+previewImageElementId);

	$('body').on('click', '.yummy_js_upload_image_button', function(e){
		e.preventDefault();

		var image_frame;
		var button = $(this);

		image_frame = wp.media({
			title: 'Custom image',
			library : {
				uploadedTo : wp.media.view.settings.post.id,
				type : 'image'
			},
			button: {
				text: 'Use this image'
			},
			multiple: false
		});

		image_frame.on('close',function() {
			// On close, get selections and save to the hidden input.
			var selection = image_frame.state().get('selection');
			var gallery_ids = new Array();
			var my_index = 0;
			selection.each(function(attachment) {
				gallery_ids[my_index] = attachment['id'];
				my_index++;
			});
			var attachmentIds = gallery_ids.join(',');
			$(inputElementId).val(attachmentIds);

			// Refresh the image preview element with AJAX.
			yummyRefreshMetaImage(attachmentIds, previewImageElementId);

			$(inputElementId).trigger('change');
		});

		// On open, get the id from the hidden input and select the appropiate images in the media manager.
		image_frame.on('open',function() {
			var current_image_ids = inputElementId.val().split(',');

			if (current_image_ids.length > 0) {
				var selection = image_frame.state().get('selection');
				current_image_ids.forEach(function(id) {
					attachment = wp.media.attachment(id);
					attachment.fetch();
					selection.add( attachment ? [ attachment ] : [] );
				});
			}
		});

		image_frame.open();
	});

	// Delete image by removing the input value and hiding the preview image.
	$('body').on('click', '.yummy-remove-file[data-target="_yummy_term_meta_image_id"]', function(e){
		e.preventDefault();
		inputElementId.val('');
		var previewImageElement = $('#'+previewImageElementId);
		if(previewImageElement.length){
			previewImageElement.hide();
		}
		$(this).hide();
	});

	$(document).ajaxSuccess(function(e, request, settings){
		var object = $.deparam(settings.data);
		if(object.action === 'add-tag' && object.post_type === 'yummy_recipe'){
			var previewImageElement = $('#'+previewImageElementId);

			if(previewImageElement.length){
				previewImageElement.hide();
			}
		}
	});

	// Ajax request to refresh the image preview.
	function yummyRefreshMetaImage(attachmentIds, previewImageElementId){
		var data = {
			action: 'yummy_get_meta_image_preview',
			attachmentId: attachmentIds,
			previewImageElementId: previewImageElementId
		};

		jQuery.get(ajaxurl, data, function(response) {
			if(response.success === true) {
				jQuery('#'+previewImageElementId).replaceWith(response.data.image);
			}
		});
	}

	(function(deparam){
		if (typeof require === 'function' && typeof exports === 'object' && typeof module === 'object') {
			try {
				var jquery = require('jquery');
			} catch (e) {
			}
			module.exports = deparam(jquery);
		} else if (typeof define === 'function' && define.amd){
			define(['jquery'], function(jquery){
				return deparam(jquery);
			});
		} else {
			var global;
			try {
			  global = (false || eval)('this'); // best cross-browser way to determine global for < ES5
			} catch (e) {
			  global = window; // fails only if browser (https://developer.mozilla.org/en-US/docs/Web/Security/CSP/CSP_policy_directives)
			}
			global.deparam = deparam(global.jQuery); // assume jQuery is in global namespace
		}
	})(function ($) {
		var deparam = function( params, coerce ) {
			var obj = {},
			coerce_types = { 'true': !0, 'false': !1, 'null': null };

			// If params is an empty string or otherwise falsy, return obj.
			if (!params) {
				return obj;
			}

			// Iterate over all name=value pairs.
			params.replace(/\+/g, ' ').split('&').forEach(function(v){
				var param = v.split( '=' ),
				key = decodeURIComponent( param[0] ),
				val,
				cur = obj,
				i = 0,

				// If key is more complex than 'foo', like 'a[]' or 'a[b][c]', split it
				// into its component parts.
				keys = key.split( '][' ),
				keys_last = keys.length - 1;

				// If the first keys part contains [ and the last ends with ], then []
				// are correctly balanced.
				if ( /\[/.test( keys[0] ) && /\]$/.test( keys[ keys_last ] ) ) {
					// Remove the trailing ] from the last keys part.
					keys[ keys_last ] = keys[ keys_last ].replace( /\]$/, '' );

					// Split first keys part into two parts on the [ and add them back onto
					// the beginning of the keys array.
					keys = keys.shift().split('[').concat( keys );

					keys_last = keys.length - 1;
				} else {
					// Basic 'foo' style key.
					keys_last = 0;
				}

				// Are we dealing with a name=value pair, or just a name?
				if ( param.length === 2 ) {
					val = decodeURIComponent( param[1] );

					// Coerce values.
					if ( coerce ) {
						val = val && !isNaN(val) && ((+val + '') === val) ? +val        // number
						: val === 'undefined'                       ? undefined         // undefined
						: coerce_types[val] !== undefined           ? coerce_types[val] // true, false, null
						: val;                                                          // string
					}

					if ( keys_last ) {
						// Complex key, build deep object structure based on a few rules:
						// * The 'cur' pointer starts at the object top-level.
						// * [] = array push (n is set to array length), [n] = array if n is
						//   numeric, otherwise object.
						// * If at the last keys part, set the value.
						// * For each keys part, if the current level is undefined create an
						//   object or array based on the type of the next keys part.
						// * Move the 'cur' pointer to the next level.
						// * Rinse & repeat.
						for ( ; i <= keys_last; i++ ) {
							key = keys[i] === '' ? cur.length : keys[i];
							cur = cur[key] = i < keys_last
							? cur[key] || ( keys[i+1] && isNaN( keys[i+1] ) ? {} : [] )
							: val;
						}

					} else {
						// Simple key, even simpler rules, since only scalars and shallow
						// arrays are allowed.

						if ( Object.prototype.toString.call( obj[key] ) === '[object Array]' ) {
							// val is already an array, so push on the next value.
							obj[key].push( val );

						} else if ( {}.hasOwnProperty.call(obj, key) ) {
							// val isn't an array, but since a second value has been specified,
							// convert val into an array.
							obj[key] = [ obj[key], val ];

						} else {
							// val is a scalar.
							obj[key] = val;
						}
					}

				} else if ( key ) {
					// No value was defined, so set something meaningful.
					obj[key] = coerce
					? undefined
					: '';
				}
			});

			return obj;
		};
		if ($) {
		  $.prototype.deparam = $.deparam = deparam;
		}
		return deparam;
	});
});
