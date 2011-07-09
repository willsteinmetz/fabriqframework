<h1>My Account</h1>
<h3>Welcome, <?php echo $_SESSION['FABMOD_USERS_displayname']; ?></h3>
<ul>
	<li><?php Fabriq::link_to('Change password', 'users', 'changePassword'); ?></li>
	<li><?php Fabriq::link_to('Update account', 'users', 'updateAccount'); ?></li>
	<li><?php Fabriq::link_to('Log out', 'users', 'logout'); ?></li>
</ul>
