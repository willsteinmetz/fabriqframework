FabriqModules = {
	hasConfiguration: function(module) {
		$.ajax({
			type: 'GET',
			url: Fabriq.build_path('fabriqmodules', 'hasConfiguration', module),
			dataType: 'json',
			success: function(data, status) {
				if (data.hasConfiguration) {
					FabriqModules.configurationForm(module);
				} else {
					FabriqModules.enable(module);
				}
			}
		});
	},
	
	configurationForm: function(module) {
		$.ajax({
			type: 'GET',
			url: Fabriq.build_path('fabriqmodules', 'configure', module),
			dataType: 'html',
			success: function(data, status) {
				$('#module-config-' + module).html(data);
				$('#module-config-' + module).parent().show();
				if ($('#ajax-callback').length > 0) {
					eval($('#ajax-callback')[0]);
				}
			}
		});
	},
	
	enable: function(module) {
		
	},
	
	disable: function(module) {
		
	}
}
