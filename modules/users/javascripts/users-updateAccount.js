UsersUpdateAccount = {
	displayPattern: /([A-z0-9]){6,24}/,
	
	init: function() {
		jQuery('#user-registration').validate({
			rules: {
				display: {
					required: true,
					displayValid: true,
					displayUnique: true
				},
				email: {
					required: true,
					email: true,
					emailUnique: true
				}
			},
			messages: {
				display: {
					required: 'You must provide a display name'
				},
				email: {
					required: 'You must provide an e-mail address',
					email: 'A valid e-mail address is required'
				}
			},
			errorElement: 'div',
			errorClass: 'validation-error'
		});
		jQuery('#display').keyup(UsersUpdateAccount.checkDisplayName);
		jQuery('#email').keyup(UsersUpdateAccount.checkEmail);
	},
	
	checkDisplayName: function(event) {
		if (jQuery('#display').val().length >= 6) {
			if (UsersUpdateAccount.displayTimer) {
				clearTimeout(UsersUpdateAccount.displayTimer);
				UsersUpdateAccount.displayTimer = null;
			}
			UsersUpdateAccount.displayTimer = setTimeout(function() {
				if (jQuery('#display').val().length >= 6) {
					var data = {
						display: jQuery('#display').val(),
						user: jQuery('#user').val()
					};
					jQuery('#save-update').attr('disabled', 'disabled');
					jQuery('#display-available').text('Checking for availability...');
					jQuery.ajax({
						url: Fabriq.build_path('users', 'checkDisplay'),
						data: data,
						type: 'POST',
						dataType: 'json',
						success: function(data, status) {
							jQuery('#display-available').removeClass('value-exists').removeClass('value-available');
							if (data.exists) {
								jQuery('#display-available').addClass('value-exists').text('Taken');
							} else {
								jQuery('#display-available').addClass('value-available').text('Available');
							}
							jQuery('#save-update').attr('disabled', '');
						}
					});
				}
			}, 500);
		}
	},
	
	checkEmail: function(event) {
		if (UsersUpdateAccount.displayTimer) {
			clearTimeout(UsersUpdateAccount.displayTimer);
			UsersUpdateAccount.displayTimer = null;
		}
		UsersUpdateAccount.displayTimer = setTimeout(function() {
			var data = {
				email: jQuery('#email').val(),
				user: jQuery('#user').val()
			};
			jQuery('#save-update').attr('disabled', 'disabled');
			jQuery('#email-available').text('Checking for availability...');
			jQuery.ajax({
				url: Fabriq.build_path('users', 'checkEmail'),
				data: data,
				type: 'POST',
				dataType: 'json',
				success: function(data, status) {
					jQuery('#email-available').removeClass('value-exists').removeClass('value-available');
					if (data.exists) {
						jQuery('#email-available').addClass('value-exists').text('Taken');
					} else {
						jQuery('#email-available').addClass('value-available').text('Available');
					}
					jQuery('#save-update').attr('disabled', '');
				}
			});
		}, 500);
	}
};

jQuery(function() {
	jQuery(document).click(function(event) {
		jQuery('#message-box').fadeOut('fast', function(event) {
			jQuery(this)
				.removeClass('errors')
				.html('');
		});
	});
	
	jQuery.validator.addMethod("displayValid", function(value, element) {
		return UsersUpdateAccount.displayPattern.test(value);
	}, 'Display name may only contain letters, numbers, and the underscore character between 6 and 24 characters long');
	jQuery.validator.addMethod('displayUnique', function(value, element) {
		return (jQuery('#display-available').text() != 'Taken');
	}, 'Given display name has been taken. Display names must be unique.');
	jQuery.validator.addMethod('emailUnique', function(value, element) {
		return (jQuery('#email-available').text() != 'Taken');
	}, 'Given e-mail address has been taken. e-mail addresses must be unique.');
	
	UsersUpdateAccount.init();
});
