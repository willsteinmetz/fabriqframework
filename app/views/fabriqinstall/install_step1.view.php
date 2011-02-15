<h1>Welcome to Fabriq Framework</h1>
<p>Before moving on to the next step, make sure of the following:</p>
<ul>
	<li>
		Be sure that the following directories have write permissions:
		<ul>
			<li><code>/config</code></li>
			<li><code>/app/controllers</code></li>
			<li><code>/app/views</code></li>
		</ul>
	</li>
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