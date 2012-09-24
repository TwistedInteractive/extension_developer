<?php
	function inputVars($name, $label = null)
	{
		if($label == null) { $label = ucfirst(str_replace('_', ' ', $name)); }

		echo sprintf('<label for="%1$s">%2$s:</label><input type="text" name="vars[%1$s]" id="%1$s" />',
			$name, $label);
	}
?>
<html>
<head>
	<title>Symphony Extension Developer</title>
	<link rel="stylesheet" type="text/css" href="css/screen.css" />
	<script type="text/javascript" src="js/global.js"></script>
</head>
<body>
	<h1>Symphony Extension Developer</h1>
	<h3>Create them extensions with ease!</h3>
	<form method="post" action="compile.php">
		<fieldset>
			<legend>General Information</legend>
			<?php
				inputVars('name');
				inputVars('description');
				inputVars('version');
				inputVars('github_url', 'GitHub URL');
			?>
        </fieldset>
        <fieldset>
            <legend>Author Information</legend>
			<?php
				inputVars('author_name');
				inputVars('author_website');
				inputVars('author_email');
				inputVars('github_name');
				inputVars('symphony_name');
			?>
        </fieldset>
		<fieldset>
			<legend>Type</legend>
			<label for="type">Type:</label>
			<select name="vars[type]" id="type">
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
					inputVars('own_type');
				?>
            </div>
		</fieldset>
		<fieldset class="type field">
			<legend>Extra options for Field</legend>
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
		<input type="submit" value="Give me the candy!" />
	</form>
</body>
</html>