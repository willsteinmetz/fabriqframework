UsersIndex = {
	ban: function(user) {
		$.ajax({
			type: 'POST',
			url: Fabriq.build_path('users', 'ban'),
			data: {
				'user': user
			},
			dataType: 'json',
			success: function(data, status) {
				
			}
		});
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
				
			}
		});
	}
};
