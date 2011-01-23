FabriqModules = {
	init: function() {
		Fabriq.UI.Overlay.init();
	},
	
	hasConfiguration: function(module) {
		var self = this;
		jQuery.ajax({
			type: 'GET',
			url: Fabriq.build_path('fabriqmodules', 'hasConfiguration', module),
			dataType: 'json',
			success: function(data, status) {
				if (data.hasConfiguration) {
					self.configurationForm(module);
				} else {
					self.enable(module);
				}
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
		
	},
	
	disable: function(module) {
		
	}
}

jQuery(function() {
	FabriqModules.init();
});
