UsersIndex = {
	ban: function(user) {
		if (confirm("Are you sure you want to ban this user?")) {
			$.ajax({
				type: 'POST',
				url: Fabriq.build_path('users', 'ban'),
				data: {
					'user': user
				},
				dataType: 'json',
				success: function(data, status) {
					if (data.success) {
						$('#user-' + user + ' td:eq(3)').text('banned');
						$('#user-' + user + ' .ban-button')
							.removeClass('ban-button')
							.addClass('enable-button')
							.text('Enable')
							.unbind('click')
							.click(function(event) {
								UsersIndex.enable(user);
							});
						$('#message-box')
							.addClass('successes')
							.append(
								$('<ul />').append(
									$('<li />').text('User has been banned')
								)
							)
							.show();
					} else {
						$('#message-box')
							.addClass('errors')
							.append(
								$('<ul />').append(
									$('<li />').text('User was unable to be banned')
								)
							)
							.show();
					}
				}
			});
		}
	},
	
	enable: function(user) {
		$.ajax({
			type: 'POST',
			url: Fabriq.build_path('users', 'enable'),
			data: {
				'user': user
			},
			dataType: 'json',
			success: function(data, status) {
				if (data.success) {
					$('#user-' + user + ' td:eq(3)').text('enabled');
					$('#user-' + user + ' .enable-button')
						.removeClass('enable-button')
						.addClass('ban-button')
						.text('Ban')
						.unbind('click')
						.click(function(event) {
							UsersIndex.enable(user);
						});
					$('#message-box')
						.addClass('successes')
						.append(
							$('<ul />').append(
								$('<li />').text('User has been enabled')
							)
						)
						.show();
				} else {
					$('#message-box')
						.addClass('errors')
						.append(
							$('<ul />').append(
								$('<li />').text('User was unable to be enabled')
							)
						)
						.show();
				}
			}
		});
	}
};

$(function() {
	$(document).click(function(event) {
		$('#message-box').fadeOut('fast', function(event) {
			$(this)
				.removeClass('errors')
				.html('');
		});
	});
});
