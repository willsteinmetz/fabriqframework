/**
 * @file fabriqui.js - DO NOT EDIT
 * @author Will Steinmetz
 * This file contains the Fabriq UI code
 * 
 * Copyright (c)2011, Ralivue.com
 * Licensed under the BSD license.
 * http://fabriqframework.com/license
 */

Fabriq.UI = {
	/**
	 * Overlay UI element used for containing information, forms,
	 * settings panels, etc that should be highlighted and separated
	 * from the main content.
	 */
	Overlay: {
		/**
		 * Initialize the Overlay element
		 */
		init: function() {
			Fabriq.UI.Overlay.overlay = jQuery('<div />')
				.addClass('fabriq-ui-overlay')
				.appendTo($('body'));
			Fabriq.UI.Overlay.content = jQuery('<div />')
				.addClass('fabriq-ui-overlay-content')
				.appendTo(Fabriq.UI.Overlay.overlay);
			Fabriq.UI.Overlay.title = jQuery('<div />')
				.addClass('fabriq-ui-overlay-title')
				.appendTo(Fabriq.UI.Overlay.overlay);
			Fabriq.UI.Overlay.overlay.hide();
			Fabriq.UI.Overlay.sizeOverlay();
			jQuery(window).resize(Fabriq.UI.Overlay.sizeOverlay);
		},
		
		/**
		 * Show the overlay element
		 */
		show: function(callback) {
			if (callback) {
				Fabriq.UI.Overlay.overlay.fadeIn('fast', callback);
			} else {
				Fabriq.UI.Overlay.overlay.fadeIn('fast');
			}
		},
		
		/**
		 * Hide the overlay element
		 */
		hide: function(callback) {
			if (callback) {
				Fabriq.UI.Overlay.overlay.fadeOut('fast', callback);
			} else {
				Fabriq.UI.Overlay.overlay.fadeOut('fast');
			}
		},
		
		/**
		 * Remove the contents of the overlay element
		 */
		emptyContent: function() {
			Fabriq.UI.Overlay.content.html('');
		},
		
		/**
		 * Adds content to the overlay element
		 * @param mixed content
		 */
		addContent: function(content) {
			Fabriq.UI.Overlay.content.append(content);
		},
		
		/**
		 * Change the content of the overlay
		 * @param mixed content
		 */
		setContent: function(content) {
			Fabriq.UI.Overlay.content.html(content);
		},
		
		/**
		 * Set the title of the overlay element
		 * @param string title
		 */
		setTitle: function(title) {
			Fabriq.UI.Overlay.title.text(title);
		},
		
		/**
		 * Resets the overlay element to be empty and removes the title
		 */
		reset: function() {
			Fabriq.UI.Overlay.emptyContent();
			Fabriq.UI.Overlay.title('');
		},
		
		/**
		 * Open the overlay element and set the title and content
		 * @param string title
		 * @param mixed content
		 */
		open: function(title, content) {
			Fabriq.UI.Overlay.setTitle(title);
			Fabriq.UI.Overlay.setContent(content);
			Fabriq.UI.Overlay.show();
		},
		
		/**
		 * Close the overlay element and remove the title and content
		 */
		close: function() {
			Fabriq.UI.Overlay.hide(function(event) {
				Fabriq.UI.Overlay.emptyContent();
				Fabriq.UI.Overlay.setTitle('');
			});
		},
		
		/**
		 * Size the overlay
		 */
		sizeOverlay: function() {
			Fabriq.UI.Overlay.overlay
				.width($(window).width())
				.height($(window).height());
			Fabriq.UI.Overlay.content
				.width($(window).width() - 110)
				.height($(window).height() - 110);
		}
	}
};
