FabriqModules = {
	init: function() {
		Fabriq.UI.Overlay.init();
		$(document).click(function(event) {
			if (jQuery('#message-box').is(':visible')) {
				jQuery('#message-box')
					.fadeOut()
					.removeClass('errors')
					.removeClass('successes')
					.removeClass('messages')
					.removeClass('warnings')
					.html('');
			}
		});
	},
	
	configurationForm: function(module) {
		jQuery.ajax({
			type: 'GET',
			url: Fabriq.build_path('fabriqmodules', 'configure', module),
			dataType: 'html',
			success: function(data, status) {
				Fabriq.UI.Overlay.open('Configure module', data);
				if (jQuery('#ajax-callback').length > 0) {
					eval(jQuery('#ajax-callback')[0]);
				}
			}
		});
	},
	
	enable: function(module) {
		jQuery.ajax({
			type: 'GET',
			url: Fabriq.build_path('fabriqmodules', 'enable', module),
			dataType: 'json',
			success: function(data, status) {
				if (data.notLoggedIn) {
					window.location = window.location;
				} else {
					if (data.success) {
						jQuery('#enable-button-' + module)
							.text('disable')
							.unbind('click')
							.click(function(event) {
								FabriqModules.disable(module);
							});
						jQuery('#install-button-' + module)
							.attr('disabled', 'disabled')
							.attr('title', 'Module must be disabled before it can be uninstalled');
						jQuery('#message-box')
							.addClass('successes')
							.html('Module enabled')
							.fadeIn();
					} else {
						jQuery('#message-box')
							.addClass('errors')
							.html('Module could not be enabled')
							.fadeIn();
					}
				}
			}
		});
	},
	
	disable: function(module) {
		jQuery.ajax({
			type: 'GET',
			url: Fabriq.build_path('fabriqmodules', 'disable', module),
			dataType: 'json',
			success: function(data, status) {
				if (data.notLoggedIn) {
					window.location = window.location;
				} else {
					if (data.success) {
						jQuery('#enable-button-' + module)
							.text('enable')
							.unbind('click')
							.click(function(event) {
								FabriqModules.enable(module);
							});
						jQuery('#install-button-' + module)
							.attr('disabled', '')
							.attr('title', '');
						jQuery('#message-box')
							.addClass('successes')
							.html('Module disabled')
							.fadeIn();
					} else {
						jQuery('#message-box')
							.addClass('errors')
							.html('Module could not be disabled')
							.fadeIn();
					}
				}
			}
		});
	},
	
	install: function(module) {
		jQuery.ajax({
			type: 'GET',
			url: Fabriq.build_path('fabriqmodules', 'install', module),
			dataType: 'json',
			success: function(data, status) {
				if (data.notLoggedIn) {
					window.location = window.location;
				} else {
					if (data.success) {
						if (jQuery('#enable-button-' + module).length == 0) {
							var enableButton = jQuery('<button />');
							enableButton.text('enable')
								.attr('id', 'enable-button-' + module)
								.click(function(event) {
									FabriqModules.enable(module);
								});
							$('tr#module-' + module).find('td.fabriqmodules-enable-col').append(enableButton);
						} else {
							jQuery('#enable-button-' + module)
								.text('enable')
								.unbind('click')
								.click(function(event) {
									FabriqModules.enable(module);
								});
						}
						jQuery('#install-button-' + module)
							.text('uninstall')
							.unbind('click')
							.click(function(event) {
								FabriqModules.uninstall(module);
							});
						jQuery('#message-box')
							.addClass('successes')
							.html('Module has been installed')
							.fadeIn();
						if (parseInt(data.hasConfiguration, 10) == 1) {
							jQuery('tr#module-' + module).find('td.fabriqmodules-config-col').html('').append(
								jQuery('<button />')
									.text('configure')
									.attr('id', 'config-button-' + module)
									.click(function(event) {
										FabriqModules.configurationForm(module);
									})
							);
						}
					} else {
						jQuery('#message-box')
							.addClass('errors')
							.html('Module could not be installed')
							.fadeIn();
					}
				}
			}
		});
	},
	
	uninstall: function(module) {
		jQuery.ajax({
			type: 'GET',
			url: Fabriq.build_path('fabriqmodules', 'uninstall', module),
			dataType: 'json',
			success: function(data, status) {
				if (data.notLoggedIn) {
					window.location = window.location;
				} else {
					if (data.success) {
						jQuery('#enable-button-' + module).remove();
						jQuery('#install-button-' + module)
							.text('install')
							.unbind('click')
							.click(function(event) {
								FabriqModules.install(module);
							});
						jQuery('#message-box')
							.addClass('successes')
							.html('Module has been uninstalled')
							.fadeIn();
						jQuery('#config-button-' + module).remove();
					} else {
						jQuery('#message-box')
							.addClass('errors')
							.html('Module could not be installed')
							.fadeIn();
					}
				}
			}
		});
	}
}

jQuery(function() {
	FabriqModules.init();
});
