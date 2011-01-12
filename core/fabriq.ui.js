/**
 * @file fabriqui.js
 * @author Will Steinmetz
 * This file contains the Fabriq UI code
 */

Fabriq.UI = {
	Overlay: {
		init: function() {
			Fabriq.UI.Overlay.overlay = $('<div />')
				.addClass('fabriq-ui-overlay')
				.width($(window).width())
				.height($(window).height());
			Fabriq.UI.Overlay.content = $('<div />')
				.addClass('fabriq-ui-overlay-content')
				.width($(window).width() - 100)
				.height($(window).height() - 100);
			Fabriq.UI.Overlay.title = $('<div />')
				.addClass('fabriq-ui-overlay-title');
		},
		
		show: function() {
			Fabriq.UI.Overlay.overlay.show();
		},
		
		hide: function() {
			Fabriq.UI.Overlay.overlay.hide();
		},
		
		emptyContent: function() {
			Fabriq.UI.Overlay.content.html('');
		},
		
		addContent: function(content) {
			Fabriq.UI.Overlay.content.append(content);
		},
		
		setTitle: function(title) {
			Fabriq.UI.Overlay.title.text(title);
		},
		
		reset: function() {
			Fabriq.UI.Overlay.emptyContent();
			Fabriq.UI.Overlay.title('');
		},
		
		open: function(title, content) {
			Fabriq.UI.Overlay.title(title);
			Fabriq.UI.Overlay.content(content);
			Fabriq.UI.Overlay.show();
		},
		
		close: function() {
			Fabriq.UI.Overlay.emptyContent();
			Fabriq.UI.Overlay.title('');
			Fabriq.UI.Overlay.hide();
		}
	}
};
