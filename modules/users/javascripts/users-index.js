UsersIndex = {
	displayPattern: /([A-z0-9]){6,24}/,
	
	init: function() {
		this.compileTemplates();
		Fabriq.UI.Overlay.init();
	},
	
	/**
	 * Compile the templates that use Handlebars.js
	 */
	compileTemplates: function() {
		this.templates = {};
		this.templates.userRole = Handlebars.compile(jQuery('#user-role-tmpl').html());
		this.templates.updateForm = Handlebars.compile(jQuery('#update-form').html());
		this.templates.addForm = Handlebars.compile(jQuery('#add-form').html());
		this.templates.newUser = Handlebars.compile(jQuery('#new-user').html());
	},
	
	ban: function(user) {
		if (confirm("Are you sure you want to ban this user?")) {
			jQuery.ajax({
				type: 'POST',
				url: Fabriq.build_path('users', 'ban'),
				data: {
					'user': user
				},
				dataType: 'json',
				success: function(data, status) {
					if (data.success) {
						jQuery('#user-' + user + ' td:eq(3)').text('banned');
						jQuery('#user-' + user + ' .ban-button')
							.removeClass('ban-button')
							.addClass('enable-button')
							.text('Enable')
							.unbind('click')
							.click(function(event) {
								UsersIndex.enable(user);
							});
						jQuery('#message-box')
							.addClass('successes')
							.append(
								jQuery('<ul />').append(
									jQuery('<li />').text('User has been banned')
								)
							)
							.show();
					} else {
						if (data.notLoggedIn) {
							window.location = window.location;
						} else {
							jQuery('#message-box')
								.addClass('errors')
								.append(
									jQuery('<ul />').append(
										jQuery('<li />').text('User was unable to be banned')
									)
								)
								.show();
						}
					}
				}
			});
		}
	},
	
	enable: function(user) {
		jQuery.ajax({
			type: 'POST',
			url: Fabriq.build_path('users', 'enable'),
			data: {
				'user': user
			},
			dataType: 'json',
			success: function(data, status) {
				if (data.success) {
					jQuery('#user-' + user + ' td:eq(3)').text('enabled');
					jQuery('#user-' + user + ' .enable-button')
						.removeClass('enable-button')
						.addClass('ban-button')
						.text('Ban')
						.unbind('click')
						.click(function(event) {
							UsersIndex.enable(user);
						});
					jQuery('#message-box')
						.addClass('successes')
						.append(
							jQuery('<ul />').append(
								jQuery('<li />').text('User has been enabled')
							)
						)
						.show();
				} else {
					if (data.notLoggedIn) {
						window.location = window.location;
					} else {
						jQuery('#message-box')
							.addClass('errors')
							.append(
								jQuery('<ul />').append(
									jQuery('<li />').text('User was unable to be enabled')
								)
							)
							.show();
					}
				}
			}
		});
	},
	
	updateUser: function(user) {
		jQuery.ajax({
			type: 'GET',
			url: Fabriq.build_path('users', 'update', user),
			dataType: 'json',
			success: function(data, status) {
				if (data.success) {
					UsersIndex.mode = 'edit';
					Fabriq.UI.Overlay.open('Update User', UsersIndex.templates.updateForm(data['user']));
					jQuery('#update-user-' + user).validate({
						onsubmit: false,
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
					jQuery('#display').keyup(UsersIndex.checkDisplayName);
					jQuery('#email').keyup(UsersIndex.checkEmail);
					jQuery('#update-roles-row').html('');
					for (var i in data.roles) {
						jQuery('#update-roles-row').append(jQuery(UsersIndex.templates.userRole(data.roles[i])));
						if (inArray(data.roles[i].id, data.user.roles)) {
							$('input[name="role' + data.roles[i].id + '"]').attr('checked', 'checked');
						}
					}
				} else {
					window.location = window.location;
				}
			}
		});
	},
	
	checkDisplayName: function(event) {
		if (jQuery('#display').val().length >= 6) {
			if (UsersIndex.displayTimer) {
				clearTimeout(UsersIndex.displayTimer);
				UsersIndex.displayTimer = null;
			}
			UsersIndex.displayTimer = setTimeout(function() {
				if (jQuery('#display').val().length >= 6) {
					if (UsersIndex.mode == 'edit') {
						var data = {
							display: jQuery('#display').val(),
							user: jQuery('.update-user:first').attr('id').toString().replace('update-user-', '')
						};
					} else {
						var data = {
							display: jQuery('#display').val()
						};
					}
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
		if (UsersIndex.displayTimer) {
			clearTimeout(UsersIndex.displayTimer);
			UsersIndex.displayTimer = null;
		}
		UsersIndex.displayTimer = setTimeout(function() {
			if (UsersIndex.mode == 'edit') {
				var data = {
					email: jQuery('#email').val(),
					user: jQuery('.update-user:first').attr('id').toString().replace('update-user-', '')
				};
			} else {
				var data = {
					email: jQuery('#email').val()
				};
			}
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
	},
	
	saveUpdate: function(user) {
		if (jQuery('#update-user-' + user).valid()) {
			var dataToSend = {
				display: jQuery('#display').val(),
				email: jQuery('#email').val(),
				submit: 'submit'
			};
			$('#update-roles-row').find('input.role').each(function(index, item) {
				var $item = $(item);
				dataToSend['role' + $item.attr('data-fabmodUsersRole')] = ($item.is(':checked')) ? 1 : 0;
			});
			jQuery.ajax({
				url: Fabriq.build_path('users', 'update', user),
				type: 'POST',
				data: dataToSend,
				dataType: 'json',
				success: function(data, status) {
					if (data.success) {
						jQuery('#message-box')
							.addClass('successes')
							.append(
								jQuery('<ul />').append(
									jQuery('<li />').text('User info has been updated')
								)
							)
							.show();
						Fabriq.UI.Overlay.close();
						jQuery('#user-display-' + user).text(jQuery('#display').val());
						jQuery('#user-email-' + user).text(jQuery('#email').val());
					} else {
						if (data.notLoggedIn) {
							window.location = window.location;
						} else {
							if (jQuery('#overlay-messages').length == 0) {
								Fabriq.UI.Overlay.content.prepend(
									jQuery('<div />')
										.attr('id', 'overlay-messages')
										.addClass('errors')
										.text('An error occurred. Please try again later.')
								);
							} else {
								jQuery('#overlay-messages')
									.addClass('errors')
									.text('An error occurred. Please try again later.');
							}
						}
					}
				}
			});
		}
	},
	
	closeConfigure: function() {
		Fabriq.UI.Overlay.close();
		UsersIndex.mode = null;
	},
	
	createUser: function() {
		UsersIndex.mode = 'add';
		jQuery.ajax({
			type: 'POST',
			url: Fabriq.build_path('users', 'getRoles'),
			dataType: 'json',
			success: function(data, status) {
				Fabriq.UI.Overlay.open('Add User', UsersIndex.templates.addForm({}));
				jQuery('#add-user').validate({
					onsubmit: false,
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
						},
						pwd: {
							required: true,
							passwordMatches: true,
							minlength: 8,
							passwordNotSame: true
						},
						confpwd: {
							required: true,
							passwordMatches: true,
							minlength: 8
						}
					},
					messages: {
						display: {
							required: 'You must provide a display name'
						},
						email: {
							required: 'You must provide an e-mail address',
							email: 'A valid e-mail address is required'
						},
						pwd: {
							minlength: 'Password must be at least 8 characters in length'
						},
						confpwd: {
							minlength: 'Password must be at least 8 characters in length'
						}
					},
					errorElement: 'div',
					errorClass: 'validation-error'
				});
				jQuery('#display').keyup(UsersIndex.checkDisplayName);
				jQuery('#email').keyup(UsersIndex.checkEmail);
				if (data.notLoggedIn) {
					window.location = window.location;
				} else {
					for (var i in data.roles) {
						jQuery('#add-roles-row').append(jQuery(UsersIndex.templates.userRole(data.roles[i])));
					}
				}
			}
		});
	},
	
	saveAdd: function() {
		if (jQuery('#add-user').valid()) {
			var dataToSend = {
				display: jQuery('#display').val(),
				email: jQuery('#email').val(),
				pwd: jQuery('#pwd').val(),
				emailUser: (jQuery('#emailuser').is(':checked')) ? true : false
			}
			$('#add-roles-row').find('input.role').each(function(index, item) {
				var $item = $(item);
				dataToSend['role' + $item.attr('data-fabmodUsersRole')] = ($item.is(':checked')) ? 1 : 0;
			});
			jQuery.ajax({
				url: Fabriq.build_path('users', 'create'),
				data: dataToSend,
				type: 'POST',
				dataType: 'json',
				success: function(data, status) {
					if (data.success) {
						jQuery('#message-box')
							.addClass('successes')
							.append(
								jQuery('<ul />').append(
									jQuery('<li />').text('User has been added')
								)
							)
							.show();
						Fabriq.UI.Overlay.close();
						jQuery('#users-list tbody').append(jQuery(UsersIndex.templates.newUser(data.user)));
					} else {
						if (data.notLoggedIn) {
							window.location = window.location;
						} else {
							if (jQuery('#overlay-messages').length == 0) {
								Fabriq.UI.Overlay.content.prepend(
									jQuery('<div />')
										.attr('id', 'overlay-messages')
										.addClass('errors')
										.text('An error occurred. Please try again later.')
								);
							} else {
								jQuery('#overlay-messages')
									.addClass('errors')
									.text('An error occurred. Please try again later.');
							}
						}
					}
				}
			});
		}
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
	
	UsersIndex.init();
	
	// add display name validation function
	jQuery.validator.addMethod("displayValid", function(value, element) {
		return UsersIndex.displayPattern.test(value);
	}, 'Display name may only contain letters, numbers, and the underscore character between 6 and 24 characters long');
	jQuery.validator.addMethod('displayUnique', function(value, element) {
		return (jQuery('#display-available').text() != 'Taken');
	}, 'Given display name has been taken. Display names must be unique.');
	jQuery.validator.addMethod('emailUnique', function(value, element) {
		return (jQuery('#email-available').text() != 'Taken');
	}, 'Given e-mail address has been taken. e-mail addresses must be unique.');
	jQuery.validator.addMethod('passwordMatches', function(value, element) {
		return (jQuery('#pwd').val() == jQuery('#confpwd').val());
	}, 'Password and confirmation password do not match');
	jQuery.validator.addMethod('passwordNotSame', function(value, element) {
		return ((jQuery('#pwd').val() != jQuery('#display').val()) && (jQuery('#pwd').val() != jQuery('#email').val()));
	}, 'For security reasons, your password cannot be the same as your display name or e-mail address');
});

function inArray(needle, haystack) {
	for (var i in haystack) {
		if (haystack[i] == needle) {
			return true;
		}
	}
	return false;
}
