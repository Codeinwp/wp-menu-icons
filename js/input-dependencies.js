/* global jQuery, window: false, log: false, document: false, console: false */
/**
 * Input dependencies
 *
 * This jQuery plugin will help you toggle the visibility of form fields that
 * depends on another field.
 *
 * To use this, you need to add some data- attributes to the input fields that
 * have 'children':
 * data-dep-children: This is where you define the selector of the children of
 *     this input
 * data-dep-scope: Selector for the scope where the children should be found
 *
 * @author  Dzikri Aziz <kvcrvt@gmail.com>
 * @version 0.1.0
 *
 */
(function( $ ) {
	'use strict';

	var instances = {};

	var defaults = {
		selector: '.hasdep',
		disable:  true
	};

	var getState = function( $el, depOn ) {
		var value    = $el.val(),
		    eqString = ( 'string' === typeof depOn && depOn === value ),
		    eqNumber = ( 'number' === typeof depOn && depOn === value ),
		    inObject = ( 'object' === typeof depOn && $.inArray( value, depOn ) > -1 );

		if ( ! $el.prop( 'disabled' ) && ( eqString || eqNumber || inObject ) ) {
			return true;
		} else {
			return false;
		}
	};

	var getChildren = function( $el, options ) {
		var childrenSelector = $el.data( 'dep-children' );

		if ( ! childrenSelector ) {
			window.log( 'jQuery.inputDependencies', 'childrenSelector is not valid.', options, $el );
			return false;
		}

		var childrenScope = $el.data( 'dep-scope' );
		if ( childrenScope  ) {
			return $el.closest( childrenScope ).find( childrenSelector );
		} else {
			return $( childrenSelector );
		}
	};

	var onChange = function( e ) {
		var $el = $( e.target ),
		    options, $children;

		// If this input is already initialized, do nothing
		// This is to prevent unnecessary actions when the change event is
		// triggered by our ajaxComplete callback
		if ( e.inputDependenciesInit && $el.data( 'inputDependenciesInit' ) ) {
			return;
		} else {
			$el.data( 'inputDependenciesInit', true );
		}

		options   = e.data;
		$children = getChildren( $el, options );

		if ( ! $children.length ) {
			return false;
		}

		$children.each(function() {
			var $child = $( this ),
			    depOn  = $child.data( 'dep-on' ),
			    show;

			if ( ! depOn ) {
				return false;
			}

			show = getState( $el, depOn );
			$child.toggle( show );

			if ( true === options.disable ) {
				$child.filter( ':input' )
					.add( $child.find( ':input' ) )
					.prop( 'disabled', ! show )
					.trigger( 'change' );
			}
		});
	};

	var init = function( selector ) {
		$( selector ).trigger({
			type:                  'change',
			inputDependenciesInit: true
		});
	};

	$.inputDependencies = function( options ) {
		options = $.extend( true, {}, defaults, options );

		if ( ! options.selector ) {
			window.log( 'jQuery.inputDependencies', 'Invalid selector.', options );
			return false;
		}

		if ( instances.hasOwnProperty( options.selector ) ) {
			window.log( 'jQuery.inputDependencies', 'Selector is already registered.', options );
			return false;
		}

		instances[ options.selector ] = options;

		// Delegate event
		$( document )
			.on( 'change', options.selector, options, onChange )
			.ajaxComplete(function() {
				init( options.selector );
			});

		// Trigger event
		init( options.selector );

		return true;
	};
}( jQuery ) );

if ( undefined === window.log ) {
	/**
	 * Usage: log('inside coolFunc',this,arguments);
	 * http://paulirish.com/2009/log-a-lightweight-wrapper-for-consolelog/
	 */
	window.log = function() {
		log.history = log.history || []; // store logs to an array for reference
		log.history.push( arguments );
		if ( this.console ) {
			console.log( Array.prototype.slice.call( arguments ) );
		}
	};
}
