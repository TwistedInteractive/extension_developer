<!doctype html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/screen.css" />
    <link rel="stylesheet" type="text/css" href="lib/prettify/prettify.css" />
    <script type="text/javascript" src="lib/prettify/prettify.js"></script>
	<script type="text/javascript" src="js/show_export.js"></script>
</head>
<body>
<ul class="accordeon">
<?php

function readFiles($dir)
{
	$files = glob($dir);
	foreach($files as $file)
	{
		if(is_file($file))
		{
			$id = 'd_'.substr(md5($file), 0, 4);
			echo '<li class="file" id="'.$id.'">
				<h4>'.$file.' <em>(click to expand)</em></h4>
				<div class="code">
				<xmp class="prettyprint linenums:5">'.file_get_contents($file).'</xmp>
				</div>
			</li>';
		} elseif(is_dir($file)) {
			readFiles($file.'/*');
		}
	}
}

readFiles('export/*');

?>
</ul>
</body>
</html>
