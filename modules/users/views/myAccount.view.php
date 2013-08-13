<h1>My Account</h1>
<h3>Welcome, <?php echo $_SESSION[Fabriq::siteTitle()]['FABMOD_USERS_displayname']; ?></h3>
<ul>
	<li><a href="<?php echo PathMap::build_path('users', 'changePassword'); ?>">Change password</a></li>
	<li><a href="<?php echo PathMap::build_path('users', 'updateAccount'); ?>">Update account</a></li>
	<li><a href="<?php echo PathMap::build_path('users', 'logout'); ?>">Log out</a></li>
</ul>
