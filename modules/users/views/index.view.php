<script id="user-role-tmpl" type="text/x-handlebars-template">
<div style="padding: 2px;">
	<input type="checkbox" name="role{{id}}" data-fabmodUsersRole="{{id}}" style="margin-left: 10px;" class="role" value="1" /> {{role}}
</div>
</script>
<script id="update-form" type="text/x-handlebars-template">
<form id="update-user-{{id}}" class="update-user">
	<div style="padding: 2px;">
		<label for="display">
			Display name: <span id="display-available" class="value-check"></span><br />
			<input type="text" value="{{display}}" name="display" id="display" size="24" maxlength="24" /><br />
			<span style="font-size: 8pt;">Display names may only contain charcters, number, and the underscore character between 6 and 24 characters long.</span>
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="email">
			e-mail address: <span id="email-available" class="value-check"></span><br />
			<input type="text" value="{{email}}" name="email" id="email" size="50" maxlength="100" /><br />
			<span style="font-size: 8pt;">Must be a valid e-mail address. This e-mail address will be used when necessary to contact the user.</span>
		</label>
	</div>
	<div style="padding: 2px;">
		<label>Roles:</label>
		<div id="update-roles-row"></div>
	</div>
	<div style="padding: 2px;">
		<button type="button" onclick="UsersIndex.saveUpdate({{id}});" id="save-update">Save</button> <button type="button" onclick="UsersIndex.closeConfigure();">Cancel</button>
	</div>
</form>
</script>
<script id="add-form" type="text/x-handlebars-template">
<form id="add-user">
	<div style="padding: 2px;">
		<label for="display">
			Display name: <span id="display-available" class="value-check"></span><br />
			<input type="text" name="display" id="display" size="24" maxlength="24" /><br />
			<span style="font-size: 8pt;">Display names may only contain charcters, number, and the underscore character between 6 and 24 characters long.</span>
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="email">
			e-mail address: <span id="email-available" class="value-check"></span><br />
			<input type="text" name="email" id="email" size="50" maxlength="100" /><br />
			<span style="font-size: 8pt;">Must be a valid e-mail address. This e-mail address will be used when necessary to contact the user.</span>
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="pwd">
			Password:<br />
			<input type="password" name="pwd" id="pwd" size="24" /><br />
			<span style="font-size: 8pt;">Passwords must be at least 8 characters long. The user will be forced to change their password after their first log in.</span>
		</label>
	</div>
	<div style="padding: 2px;">
		<label for="confpwd">
			Confirm password:<br />
			<input type="password" name="confpwd" id="confpwd" size="24" /><br />
		</label>
	</div>
	<div style="padding: 2px;">
		<label>Roles:</label>
		<div id="add-roles-row"></div>
	</div>
	<div style="padding: 2px;">
		<input type="checkbox" value="1" name="emailuser" id="emailuser" /> e-mail new user their login credentials
	</div>
	<div style="padding: 2px;">
		<button type="button" onclick="UsersIndex.saveAdd();" id="save-update">Save</button> <button type="button" onclick="UsersIndex.closeConfigure();">Cancel</button>
	</div>
</form>
</script>
<script id="new-user" type="text/x-handlebars-template">
		<tr id="user-{{id}}" class="user-info">
			<td id="user-display-{{id}}">{{display}}</td>
			<td id="user-email-{{id}}">{{email}}</td>
			<td>active</td>
			<td>enabled</td>
			<td>
				<button type="button" class="edit-button" id="edit-button-{{id}}" onclick="UsersIndex.updateUser({{id}});">Edit</button> 
				<button type="button" class="ban-button" id="ban-button-{{id}}" onclick="UsersIndex.ban({{id}});">Ban</button>
			</td>
		</tr>
</script>
<h1>Manage users</h1>
<p>
	<button type="button" onclick="UsersIndex.createUser();">Add user</button>
</p>
<div class="message" style="display: none;" id="message-box"></div>

<table cellpadding="0" cellspacing="0" style="width: 100%;" id="users-list">
	<thead>
		<tr>
			<th>Display name</th>
			<th>e-mail</th>
			<th>Status</th>
			<th>Enabled</th>
			<th>Operations</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($users as $user): ?>
		<tr id="user-<?php echo $user->id; ?>" class="user-info">
			<td id="user-display-<?php echo $user->id; ?>"><?php echo $user->display; ?></td>
			<td id="user-email-<?php echo $user->id; ?>"><?php echo $user->email; ?></td>
			<td><?php echo ($user->status == 1) ? 'active' : 'inactive'; ?></td>
			<td><?php echo ($user->banned == 1) ? 'banned' : 'enabled'; ?></td>
			<td>
				<button type="button" class="edit-button" id="edit-button-<?php echo $user->id; ?>" onclick="UsersIndex.updateUser(<?php echo $user->id; ?>);">Edit</button> 
<?php
if ($user->id != $_SESSION['FABMOD_USERS_userid']):
	if ($user->banned == 1):
?>
					<button type="button" class="enable-button" id="enable-button-<?php echo $user->id; ?>" onclick="UsersIndex.enable(<?php echo $user->id; ?>);">Enable</button>
	<?php else: ?>
					<button type="button" class="ban-button" id="ban-button-<?php echo $user->id; ?>" onclick="UsersIndex.ban(<?php echo $user->id; ?>);">Ban</button>
<?php
	endif;
endif;
?>
			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>