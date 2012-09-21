<?php

// Set the vars:
$vars = array();
foreach($_POST['vars'] as $key => $value)
{
	$vars[strtoupper($key)] = $value;
}

$vars['CLASS_NAME'] 	= str_replace(' ', '_', ucfirst(strtolower($vars['NAME'])));
$vars['DATE'] 			= date('Y-m-d');
$vars['FOLDER_NAME']	= str_replace(' ', '_', strtolower($vars['NAME']));