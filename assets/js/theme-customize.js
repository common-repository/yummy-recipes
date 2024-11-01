( function( $ ) {

	wp.customize( 'yummy_big_card_style', function( value ) {
		value.bind( function( newval ) {
			$(".yummy-big-card").removeClass (function (index, className) {
				return (className.match (/(^|\s)yummy-big-card-\S+/g) || []).join(' ');
			});
			$('.yummy-big-card').addClass('yummy-big-card-style-'+newval);
		} );
	} );

	wp.customize( 'yummy_small_card_style', function( value ) {
		value.bind( function( newval ) {
			$(".yummy-small-card").removeClass (function (index, className) {
				return (className.match (/(^|\s)yummy-small-card-\S+/g) || []).join(' ');
			});
			$('.yummy-small-card').addClass('yummy-small-card-style-'+newval);
		} );
	} );

	wp.customize( 'yummy_color_big_card_background', function( value ) {
		value.bind( function( newval ) {
			$('.yummy-big-card').css('background-color', newval);
		} );
	} );

	wp.customize( 'yummy_color_card_border', function( value ) {
		value.bind( function( newval ) {
			$('.yummy-big-card').css('border-color', newval);
			$('.yummy-big-card-style-classic button.yummy-change-servings').css('border-color', newval);
			$('.yummy-big-card-style-classic .yummy-nutrition-facts').css('border-color', newval);
			$('.yummy-big-card-style-classic .yummy-big-card-top-content').css('border-top-color', newval);
			$('.yummy-big-card-style-classic .yummy-big-card-top').css('border-bottom-color', newval);
			$('.yummy-big-card-style-classic .yummy-big-card-description').css('border-bottom-color', newval);
			$('.yummy-big-card-style-classic .yummy-big-card-ingredients').css('border-bottom-color', newval);
			$('.yummy-big-card-style-classic .yummy-nutrition-facts-header').css('border-bottom-color', newval);
			$('.yummy-big-card-style-classic .yummy-nutrition-facts-row').css('border-bottom-color', newval);
			$('.yummy-big-card-style-classic .yummy-big-card-buttons').css('border-top-color', newval);
			$('.yummy-big-card-style-classic .yummy-big-card-buttons-print-bookmark').css('border-left-color', newval);

			$('.yummy-small-card-style-classic').css('border-color', newval);
			$('.yummy-small-card-style-classic .yummy-small-card-content').css('border-top-color', newval);
			$('.yummy-small-card-style-classic .yummy-small-card-author').css('border-top-color', newval);
		} );
	} );

	wp.customize( 'yummy_color_big_card_top_link', function( value ) {
		value.bind( function( newval ) {
			$('.yummy-big-card-top a').css('color', newval);
		} );
	} );

	wp.customize( 'yummy_color_big_card_top', function( value ) {
		value.bind( function( newval ) {
			if ($('.yummy-big-card').hasClass('yummy-big-card-style-hero')) {
				$('.yummy-big-card-top-content').css('background', newval);
			} else {
				$('.yummy-big-card-top').css('background', newval);
			}

			var color_contrast = yummy_get_contrast( newval, 'dark', 'light' );

			$('.yummy-big-card').removeClass('yummy-big-card-top-is-light yummy-big-card-top-is-dark');
			$('.yummy-big-card').addClass('yummy-big-card-top-is-'+color_contrast);
		} );
	} );

	wp.customize( 'yummy_color_small_card_background', function( value ) {
		value.bind( function( newval ) {
			$('.yummy-small-card-style-modern a').css('background-color', newval);

			var color_contrast = yummy_get_contrast( newval, 'dark', 'light' );

			$('.yummy-small-card').removeClass('yummy-small-card-is-light yummy-small-card-is-dark');
			$('.yummy-small-card').addClass('yummy-small-card-is-'+color_contrast);
		} );
	} );

	wp.customize( 'yummy_color_big_card_top_image_border', function( value ) {
		value.bind( function( newval ) {
			$('.yummy-big-card-image img').css('border-color', newval);
		} );
	} );

	wp.customize( 'yummy_color_instructions_step_background', function( value ) {
		value.bind( function( newval ) {
			$('head').append('<style>.yummy-instructions ol > li:before{background-color:'+newval+' !important}</style>');

			var color_contrast = yummy_get_contrast(newval, 'dark', 'light');

			$('.yummy-big-card').removeClass('yummy-instructions-step-is-light yummy-instructions-step-is-dark');
			$('.yummy-big-card').addClass('yummy-instructions-step-is-'+color_contrast);
		} );
	} );

	wp.customize( 'yummy_color_stars', function( value ) {
		value.bind( function( newval ) {
			$('svg.yummy-icon-stars').css('fill', newval);
			$('input.yummy-input-radio-star + label').css('color', newval);
		} );
	} );

	wp.customize( 'yummy_color_social_icons', function( value ) {
		value.bind( function( newval ) {
			$('.yummy-icon-button-social').css('background-color', newval);
		} );
	} );

} )( jQuery );

// Calculates color contrast.
function yummy_get_contrast( hex, dark_return, light_return ) {

	var rgb = yummy_hex2rgb( hex );
	var colors = rgb.split(',');
	var r = colors[0];
	var g = colors[1];
	var b = colors[2];

	var yiq = ((r*299)+(g*587)+(b*114))/1000;
	return (yiq >= 155) ? light_return : dark_return;
}

// Converts HEX color value to RGB.
function yummy_hex2rgb( hex ) {

	hex = hex.replace('#', '');
	var r = parseInt(hex.substr(0,2),16);
	var g = parseInt(hex.substr(2,2),16);
	var b = parseInt(hex.substr(4,2),16);

	return r + ',' + g + ',' + b;
}
