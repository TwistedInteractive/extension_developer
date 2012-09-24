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

/**
 * Convert 'simple' format to SQL:
 * @param $str
 */
function createSQL($str)
{
	$sqlArr = array();
	$a = explode("\n", $str);
	foreach($a as $line)
	{
		$b = explode(',', $line);
		$sqlArr[] = str_replace(array("\r", "\n"), '', "\t\t\t  ".'`'.$b[0].'` '.strtoupper($b[1]));
	}
	return implode(",\n", $sqlArr);
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
$vars['CLASS_NAME'] 			= str_replace(' ', '_', ucfirst(strtolower($vars['NAME'])));
$vars['DATE'] 					= date('Y-m-d');
$vars['FOLDER_NAME']			= str_replace(' ', '_', strtolower($vars['NAME']));
$vars['FIELD_CLASS_NAME']		= str_replace(' ', '_', $vars['FIELD_NAME']);
$vars['FIELD_FILE_NAME']		= strtolower($vars['FIELD_CLASS_NAME']);
$vars['FIELD_FIELD_SQL']		= createSQL($_POST['field_field_sql']);
$vars['FIELD_DATA_SQL']			= createSQL($_POST['field_data_sql']);
$vars['INSTALL_INSTRUCTIONS']	= '';
$vars['UNINSTALL_INSTRUCTIONS']	= '';

// Check if field installation instruction should be submitted:
if($vars['TYPE'] == 'Field')
{
	// Installation instructions:
	$vars['INSTALL_INSTRUCTIONS'] .= '// Create field database:
		Symphony::Database()->query("
			CREATE TABLE IF NOT EXISTS `tbl_fields_'.$vars['FIELD_FILE_NAME'].'` (
				`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`field_id` INT(11) UNSIGNED NOT NULL,
'.$vars['FIELD_FIELD_SQL'].'
				PRIMARY KEY (`id`),
				KEY `field_id` (`field_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		';
	// Uninstall instructions:
	$vars['UNINSTALL_INSTRUCTIONS'] .= '// Drop tables:
		Symphony::Database()->query("DROP TABLE `tbl_fields_'.$vars['FIELD_FILE_NAME'].'`");
		';
}

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

// Check if include_assets is set:
if(isset($vars['INCLUDE_ASSETS']))
{
	if(!isset($_POST['delegate']['InitaliseAdminPageHead'])) {
		$_POST['delegate']['InitaliseAdminPageHead'] = '/backend/';
	}
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

	$code = '';

	if(isset($vars['INCLUDE_ASSETS']) && $name == 'InitaliseAdminPageHead')
	{
		$code .= '// Add JavaScript and CSS to the header:
		Administration::instance()->Page->addScriptToHead(URL.\'/extensions/'.$vars['FOLDER_NAME'].'/assets/global.js\');
		Administration::instance()->Page->addStylesheetToHead(URL.\'/extensions/'.$vars['FOLDER_NAME'].'/assets/screen.css\');

		';
	}

	$vars['DELEGATES_FUNCTIONS'][] = "\t".'/*
	 * Delegate \''.$name.'\' function
	 * @param $context
	 *  Provides the following parameters:
'.$parameters.'	 */
	public function action'.$name.'($context)
	{
		'.$code.'// Your code goes here...
	}
	';
}

$vars['DELEGATES_FUNCTIONS'] = implode("\n", $vars['DELEGATES_FUNCTIONS']);
$vars['DELEGATES_ARRAY'] = implode(",\n", $vars['DELEGATES_ARRAY']);

// And last but not least, copy & change the template files:

// Flush export directory:
flushDir('export');

function copyFiles($from, $to, $vars)
{
	$templateFiles = glob($from);
	foreach($templateFiles as $file)
	{
		if(is_file($file))
		{
			$content  = file_get_contents($file);
			$filename = $to.'/'.basename($file);
			foreach($vars as $key => $value)
			{
				$content = str_replace('{{'.$key.'}}', $value, $content);
				$filename = str_replace('{{'.$key.'}}', $value, $filename);
			}
			// Save:
			if(!file_exists($to)) { mkdir($to); }
			file_put_contents($filename, $content);
		}
	}
}

// Copy Template files:
copyFiles('tpl/*', 'export', $vars);
if(isset($vars['INCLUDE_ASSETS'])) { copyFiles('tpl/assets/*', 'export/assets', $vars); }

// Fields:
if($vars['TYPE'] == 'Field') { copyFiles('tpl/fields/*', 'export/fields', $vars); }

