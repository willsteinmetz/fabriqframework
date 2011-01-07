<h1>Manage users</h1>

<div class="message" style="display: none;" id="message-box"></div>

<table cellpadding="0" cellspacing="0" style="width: 100%;">
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
			<td><?php echo $user->display; ?></td>
			<td><?php echo $user->email; ?></td>
			<td><?php echo ($user->status == 1) ? 'active' : 'inactive'; ?></td>
			<td><?php echo ($user->banned == 1) ? 'banned' : 'enabled'; ?></td>
			<td>
				<button class="edit-button" id="edit-button-<?php echo $user->id; ?>">Edit</button> 
<?php
if ($user->id != $_SESSION['FABMOD_USERS_userid']):
	if ($user->banned == 1):
?>
					<button class="enable-button" id="enable-button-<?php echo $user->id; ?>" onclick="UsersIndex.enable(<?php echo $user->id; ?>);">Enable</button>
	<?php else: ?>
					<button class="ban-button" id="ban-button-<?php echo $user->id; ?>" onclick="UsersIndex.ban(<?php echo $user->id; ?>);">Ban</button>
<?php
	endif;
endif;
?>
			</td>
		</tr>
		<tr id="user-configure-<?php echo $user->id; ?>" style="display: none;" class="user-configure">
			<td colspan="5"></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>