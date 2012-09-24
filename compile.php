<?php

require_once('delegates.php');

// Functions:
/**
 * Flush a directory
 * @param $dirName
 * @param bool $include_subdirs
 */
function flushDir($dirName, $include_subdirs = true)
{
	$files = glob($dirName.'/*');
	foreach($files as $file){
		if(is_file($file))
		{
			unlink($file);
		} elseif(is_dir($file) && $include_subdirs) {
			flushDir($file);
		}
	}
}

// Set the vars:
$vars = array();
foreach($_POST['vars'] as $key => $value)
{
	$vars[strtoupper($key)] = $value;
}

// Type:
if($vars['TYPE'] == 'Own') { $vars['TYPE'] = $vars['OWN_TYPE']; }

// Extra data:
$vars['CLASS_NAME'] 	= str_replace(' ', '_', ucfirst(strtolower($vars['NAME'])));
$vars['DATE'] 			= date('Y-m-d');
$vars['FOLDER_NAME']	= str_replace(' ', '_', strtolower($vars['NAME']));

// Author data:
if(!empty($vars['AUTHOR_NAME']))
{
	$vars['AUTHOR_XML'] = "\t\t".'<author>'."\n";
	$vars['AUTHOR_XML'] .= "\t\t\t".'<name';
	if(!empty($vars['GITHUB_NAME'])) { $vars['AUTHOR_XML'] .= ' github="'.$vars['GITHUB_NAME'].'"'; }
	if(!empty($vars['SYMPHONY_NAME'])) { $vars['AUTHOR_XML'] .= ' symphony="'.$vars['SYMPHONY_NAME'].'"'; }
	$vars['AUTHOR_XML'] .= '>'.$vars['AUTHOR_NAME'].'</name>'."\n";
	if(!empty($vars['AUTHOR_EMAIL'])) { $vars['AUTHOR_XML'] .= "\t\t\t".'<email>'.$vars['AUTHOR_EMAIL'].'</email>'."\n"; }
	if(!empty($vars['AUTHOR_WEBSITE'])) { $vars['AUTHOR_XML'] .= "\t\t\t".'<website>'.$vars['AUTHOR_WEBSITE'].'</website>'."\n"; }
	$vars['AUTHOR_XML'] .= "\t\t".'</author>';
} else {
	$vars['AUTHOR_XML'] = '';
}

// Functions, according to chosen delegates:
$vars['DELEGATES_ARRAY'] = array();
$vars['DELEGATES_FUNCTIONS'] = array();
foreach($_POST['delegate'] as $name => $context)
{
	$vars['DELEGATES_ARRAY'][] = "\t\t\t".'array(
				\'page\'		=> \''.$context.'\',
				\'delegate\'	=> \''.$name.'\',
				\'callback\'	=> \'action'.$name.'\'
			)';

	$contextArr = $delegatesArr[$context];
	$parameters = '';
	foreach($contextArr as $arr)
	{
		if($arr['name'] == $name)
		{
			foreach($arr['parameters'] as $parameterXML)
			{
				$attr = $parameterXML->attributes();
				$desc = $parameterXML->xpath('description/p');
				$parameters .= "\t".' *  - '.$attr['name'].' ('.$attr['type'].') : '.(string)$desc[0]."\n";
			}
		}
	}

	$vars['DELEGATES_FUNCTIONS'][] = "\t".'/*
	 * Delegate \''.$name.'\' function
	 * @param $context
	 *  Provides the following parameters:
'.$parameters.'	 */
	public function action'.$name.'($context)
	{
		// Your code goes here...
	}
	';
}

$vars['DELEGATES_FUNCTIONS'] = implode("\n", $vars['DELEGATES_FUNCTIONS']);
$vars['DELEGATES_ARRAY'] = implode(",\n", $vars['DELEGATES_ARRAY']);

// And last but not least, copy & change the template files:

// Flush export directory:
flushDir('export');

// Copy Template files:
$templateFiles = glob('tpl/*');
foreach($templateFiles as $file)
{
	$content = file_get_contents($file);
	foreach($vars as $key => $value)
	{
		$content = str_replace('{{'.$key.'}}', $value, $content);
	}
	// Export:
	$filename = 'export/'.basename($file);
	file_put_contents($filename, $content);
}