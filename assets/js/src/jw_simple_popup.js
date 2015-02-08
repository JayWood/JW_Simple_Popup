/**
 * JW Simple Popup
 * http://plugish.com
 *
 * Copyright (c) 2015 Jay Wood
 * Licensed under the GPLv2+ license.
 */

/*jslint browser: true */
/*global jQuery:false jwsp:true */

window.Jw_Simple_Popup = (function(window, document, $, undefined){
	'use strict';

	var app = {};
	
	app.timer = null;
    app.cookie_name = 'jwsp_popup';

	app.cache = function(){
		app.$dialog = $( '.jw_simple_popup.wrapper' );
		app.$overlay = $( '.jw_simple_popup.overlay' );
		app.$close_button = app.$dialog.find( '.jwsp.close-button' );
        app.cookie = $.cookie( app.cookie_name );
	};

	app.init = function() {
		app.cache();
		app.center_and_resize();

		if( jwsp.modal ){
			app.$close_button.remove();
		} else {
			$( 'html' ).on( 'click', 'body', app.maybe_close );
		}

		$( 'body' ).on( 'click', '.jw_simple_popup.wrapper .close-button', app.close );	

		app.timer = setTimeout( app.open, 1000 );

		$( window ).resize( function(){
			clearTimeout( app.timer );
			app.timer = setTimeout( function(){
				return app.center_and_resize( true );
			}, 500 );
		});
	};

	app.close = function( evt ){
		evt.preventDefault();
		app.$overlay.fadeOut( 200 );
		$( 'body' ).removeClass( 'stop-scrolling' );
        $.cookie( app.cookie_name, true, { path: '/' } );
	};

	app.maybe_close = function( evt ){
		var target = $( evt.target );
		if ( target.hasClass( 'overlay' ) ){
			app.close( evt );
		}
	};

	app.open = function(){
        if( ! app.cookie ){
            $( 'body' ).addClass( 'stop-scrolling' );
            app.$overlay.fadeIn( 200 );
        }
	};

	app.center_and_resize = function( animate ){
		var height = app.$dialog.height(),
			w_height = window.innerHeight;

		if ( 0 === height ){
			app.$overlay.css( { 'position': 'absolute', 'visibility':'hidden', 'display':'block' } );
			height = app.$dialog.outerHeight( true );
			app.$overlay.removeAttr('style');
		}

        var new_top = ( w_height - height ) / 2;

        // @TODO: Allow the user to set an option to limit the height of the dialog
        // if the dialog if larger than the height of the window.
		if ( animate ){

			app.$dialog.animate( { 'top': new_top }, 500 );

        } else {

            // Sets the height of the dialog to the height of the
            // window ( less 60 pixels )
            // app.$dialog.css( { 'height': w_height - 60 } );

            if ( w_height < height ){
                app.$dialog.css( { 'top': 30 } );
            } else {
                app.$dialog.css( {'top': new_top } );
            }

        }

		// app.$dialog.css( { 'height': w_height - 60 } );
	};

	$(document).ready( app.init );

	return app;

})(window, document, jQuery);
