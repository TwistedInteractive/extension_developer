<?php
	function inputVars($name, $label = null)
	{
		if($label == null) { $label = ucfirst($name); }

		echo sprintf('<label for="%1$s">%2$s:</label><input type="text" name="vars[%1$s]" id="%1$s" />',
			$name, $label);
	}
?>
<html>
<head>
	<title>Symphony Extension Developer</title>
	<link rel="stylesheet" type="text/css" href="css/screen.css" />
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
			?>
        </fieldset>
		<fieldset>
			<legend>Type</legend>

		</fieldset>
		<fieldset>
			<legend>Delegates</legend>
			<?php
				// Pull them delegates from the Symphony site:
				@include 'delegates.php';

				foreach($delegatesArr as $section)
				{
					echo '<h4>'.$section['name'].'</h4>';
					foreach($section['items'] as $delegate)
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
			?>
		</fieldset>
		<input type="submit" value="Give me the candy!" />
	</form>
</body>
</html>