<h1>Welcome to Fabriq Framework</h1>
<p>Before starting the installation, make sure of the following:</p>
<ul>
	<li>
		If you are setting up a site other than the default site, be sure to read the /sites/README.txt file
		<ul>
			<li>If you have to create a new site's directory, refresh this page after creating the directory</li>
		</ul>
	</li>
	<li>Be sure that <code>sites/<?php echo FabriqStack::site(); ?>/config</code>, <code>sites/<?php echo FabriqStack::site(); ?>/app/controllers</code>, and <code>sites/<?php echo FabriqStack::site(); ?>/app/views</code> directories has write permissions</li>
	<li>
		Be sure to have the following details about the database you are using available:
		<ul>
			<li>Database name</li>
			<li>Database username with privileges to the database</li>
			<li>Database password</li>
			<li>Database server</li>
		</ul>
	</li>
</ul>
<p style="text-align: right;">
	<input type="button" value="Next step &raquo;" onclick="window.location = '<?php echo PathMap::build_path('fabriqinstall', 'install', 2); ?>';" />
</p>