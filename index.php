<?php
/**
 * Default vars input box
 * @param $name
 * @param null $label
 */
function inputVars($name, $label = null, $debug = '')
{
	if($label == null) { $label = ucfirst(str_replace('_', ' ', $name)); }

	echo sprintf('<label for="%1$s">%2$s:</label><input data-debug="%3$s" type="text" name="vars[%1$s]" id="%1$s" />',
		$name, $label, $debug);
}

/**
 * Simple dropdown with options for vars
 * @param $name
 * @param $options
 * @param null $label
 */
function inputVarsSelect($name, $options, $label = null)
{
	if($label == null) { $label = ucfirst(str_replace('_', ' ', $name)); }
	echo sprintf('<label for="%1$s">%2$s:</label><select name="vars[%1$s]" id="%1$s">',
		$name, $label);
	foreach($options as $key => $value)
	{
		echo '<option value="'.$key.'">'.$value.'</option>';
	}
	echo '</select>';
}

/**
 * Simple checkbox for vars
 * @param $name
 * @param $label
 */
function inputVarsCheckbox($name, $label)
{
	if($label == null) { $label = ucfirst(str_replace('_', ' ', $name)); }
	echo sprintf('<label class="checkbox" for="%1$s"><input type="checkbox" name="vars[%1$s]" id="%1$s" /> %2$s</label>',
		$name, $label);
}

?><!doctype html>
<html>
<head>
	<title>Symphony Extension Developer</title>
	<link rel="stylesheet" type="text/css" href="css/screen.css" />
	<script type="text/javascript" src="js/global.js"></script>
</head>
<body>
	<article id="main">
		<div class="inner">
			<h1>Symphony Extension Developer</h1>
			<h3>Create them extensions with ease!</h3>
		<!--	<a href="#" id="testdata">debug: fill with test data</a>-->
			<?php
				$errors = array();
				if(!is_writable('export')) { $errors[] = 'The folder <code>export</code> is not writable.'; }
				if(!is_writable('tmp')) { $errors[] = 'The folder <code>export</code> is not writable.'; }
				if(!class_exists('ZipArchive')) { $errors[] = 'PHPs\' <code>ZipArchive</code> module not found. Zip cannot be created.
					You can still create your extensions, but after running the script, you have to manually download the content of the <code>export</code>-folder.'; }
				if(!empty($errors))
				{
					echo '<div class="error"><h4>Oh oh...</h4><p>Before proceeding, please fix the following issues.</p><ul>';
					foreach($errors as $error) {
						echo '<li>'.$error.'</li>';
					}
					echo '</ul></div>';
				}
			?>
			<form method="post" action="compile.php">
				<fieldset>
					<legend>General Information</legend>
					<?php
						inputVars('name', null, 'Test');
						inputVars('description', null, 'This is a test description');
						inputVars('version', null, '1.0');
						inputVars('github_url', 'GitHub URL', 'https://github.com/kanduvisla/extension_developer');
					?>
				</fieldset>
				<fieldset>
					<legend>Author Information</legend>
					<?php
						inputVars('author_name', null, 'John Doe');
						inputVars('author_website', null, 'http://www.johndoe.org');
						inputVars('author_email', null, 'me@johndoe.org');
						inputVars('github_name', null, 'john_doe');
						inputVars('symphony_name', null, 'john_doe');
					?>
				</fieldset>
				<fieldset>
					<legend>Type</legend>
					<label for="type">Type:</label>
					<select name="vars[type]" id="type">
						<option>Select a type...</option>
						<option value="Event">Event - for extensions primarily providing frontend events</option>
						<option value="Field">Field - for new field types</option>
						<option value="Interface">Interface - if you modify Symphony's UI</option>
						<option value="Membership">Membership - for user roles, Symphony users or frontend profiles</option>
						<option value="Multilingual">Multilingual - allowing multilingual content</option>
						<option value="Multimedia">Multimedia - images, video, uploads or media management</option>
						<option value="Text Formatter">Text Formatter - text formatting and WYSIWYG editors</option>
						<option value="Third Party Integration">Third Party Integration - such as MailChimp, Basecamp, Google Analytics etc</option>
						<option value="Translation">Translation - backend UI translation ("language packs")</option>
						<option value="Workflow">Workflow - if the extension provides new or modifies existing Symphony workflows</option>
						<option value="Other">Other - everything else</option>
						<option value="Own">Specify your own type...</option>
					</select>
					<div class="own-type">
						<?php
							inputVars('own_type', null, 'Space Time Continuum Reaper');
						?>
					</div>
				</fieldset>
				<fieldset class="type field">
					<legend>Extra options for Field</legend>
					<?php
						inputVars('field_name', null, 'Test Field');
						inputVarsSelect('field_default_show_column', array('yes'=>'Yes', 'no'=>'No'), 'Show Column by default');
						inputVarsSelect('field_default_required', array('yes'=>'Yes', 'no'=>'No'), 'Required by default');
						inputVarsSelect('field_default_location', array('main'=>'Main content', 'sidebar'=>'Sidebar'), 'Default location');
					?>
					<label for="field_field_sql">Database fields for field:<br /><em>Use simple format: [fieldname],[fieldtype]</em></label>
					<textarea name="field_field_sql" id="field_field_sql">related_field,int(11)
		send_notifications,bool</textarea>
					<label for="field_data_sql">Database fields for data:<br /><em>Use simple format: [fieldname],[fieldtype]</em></label>
					<textarea name="field_data_sql" id="field_data_sql">handle,tinytext
		value,mediumtext
		amount,int(11)
		checked,bool</textarea>
					<p class="info"><em>If you want to use more complex SQL-statements, or need to create more than one table, edit the PHP-files manualy after creation.</em></p>
					<?php
						inputVarsCheckbox('field_parse_xsl', 'Use XSL to create the HTML in publish pages (Trust me, this will change the way you write extensions!).');
					?>
				</fieldset>
				<fieldset>
					<legend>Delegates</legend>
					<?php
						// Pull them delegates from the Symphony site:
						@include 'delegates.php';

						foreach($delegatesArr as $contextName => $delegates)
						{
							if(!empty($delegates))
							{
								echo '<h4>'.$contextName.'</h4>';
								foreach($delegates as $delegate)
								{
									echo sprintf('
										<label class="delegate">
											<input type="checkbox" name="delegate[%1$s]" value="%2$s" />
											%1$s<br />
											<span>%2$s</span>
										</label>
									', $delegate['name'], $delegate['page']);
								}
							}
						}
					?>
				</fieldset>
				<fieldset>
					<legend>Content Pages</legend>
					<?php
						inputVarsCheckbox('include_content', 'Include a content page.');
					?>
				</fieldset>
				<fieldset>
					<legend>Other Options</legend>
					<?php
						inputVarsCheckbox('include_assets', 'Include JavaScript and CSS on each page in the admin.');
						inputVarsCheckbox('reference_driver', 'Create references to the driver-class.');
					?>
				</fieldset>
				<input type="submit" value="Download me the candy!" />
				<input type="submit" name="update" value="Update the candy!" />
			</form>
        </div>
    </article>
	<aside id="live">
		<iframe src="about:blank" name="live" />
	</aside>
</body>
</html>