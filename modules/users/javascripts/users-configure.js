UsersConfigure = {
	init: function() {
		$('#users-module-configuration').css({opacity: 0.5});
		$('#users-module-configuration input').attr('disabled', 'disabled');
		$('#useCustom').change(UsersConfigure.toggleForm);
	},
	
	toggleForm: function(event) {
		if ($(this).is(':checked')) {
			$('#users-module-configuration').css({opacity: 1});
			$('#users-module-configuration input').attr('disabled', '');
		} else {
			$('#users-module-configuration').css({opacity: 0.5});
			$('#users-module-configuration input').attr('disabled', 'disabled');
		}
	},
	
	saveConfiguration: function() {
		
	}
};

$(function() {
	UsersConfigure.init();
});
