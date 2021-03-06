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
	if(is_array($files) && !empty($files))
	{
		foreach($files as $file){
			if(is_file($file))
			{
				unlink($file);
			} elseif(is_dir($file) && $include_subdirs) {
				flushDir($file);
				rmdir($file);
			}
		}
	}
}
/**
 * Convert 'simple' format to SQL:
 * @param $str
 */
function createSQL($str)
{
	if(!empty($str))
	{
		$sqlArr = array();
		$a = explode("\n", $str);
		foreach($a as $line)
		{
			$b = explode(',', $line);
			$sqlArr[] = str_replace(array("\r", "\n"), '', "\t\t\t  ".'`'.$b[0].'` '.strtoupper($b[1]));
		}
		return ",\n".implode(",\n", $sqlArr);
	} else {
		return '';
	}
}

/**
 * Zip a Folder
 * @param $source
 * @param $destination
 * @return bool
 */
function Zip($source, $destination)
{
	if (!extension_loaded('zip') || !file_exists($source)) {
		return false;
	}

	$zip = new ZipArchive();
	if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
		return false;
	}

	$source = str_replace('\\', '/', realpath($source));

	if (is_dir($source) === true)
	{
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

		foreach ($files as $file)
		{
			$file = str_replace('\\', '/', $file);

			// Ignore "." and ".." folders
			if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
				continue;

			$file = realpath($file);

			if (is_dir($file) === true)
			{
				$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
			}
			else if (is_file($file) === true)
			{
				$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
			}
		}
	}
	else if (is_file($source) === true)
	{
		$zip->addFromString(basename($source), file_get_contents($source));
	}

	return $zip->close();
}

// Set the vars:
$vars = array();
foreach($_REQUEST['vars'] as $key => $value)
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
$vars['FIELD_FIELD_SQL']		= createSQL($_REQUEST['field_field_sql']);
$vars['FIELD_DATA_SQL']			= createSQL($_REQUEST['field_data_sql']);
$vars['INSTALL_INSTRUCTIONS']	= '';
$vars['UNINSTALL_INSTRUCTIONS']	= '';
$vars['FIELD_VARS']				= array();
$vars['FIELD_CONSTRUCTOR']		= array();
$vars['CONTENT_VARS']			= array();
$vars['CONTENT_CONSTRUCTOR']	= array();

// Check if field installation instruction should be submitted:
if($vars['TYPE'] == 'Field')
{
	// Installation instructions:
	$vars['INSTALL_INSTRUCTIONS'] .= '// Create field database:
		Symphony::Database()->query("
			CREATE TABLE IF NOT EXISTS `tbl_fields_'.$vars['FIELD_FILE_NAME'].'` (
				`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`field_id` INT(11) UNSIGNED NOT NULL'.$vars['FIELD_FIELD_SQL'].',
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

// Check if there should be a reference to the driver class:
if(isset($vars['REFERENCE_DRIVER'])) {
	$vars['FIELD_VARS'][] = 'protected $driver;';
	$vars['FIELD_CONSTRUCTOR'][] = '$this->_driver = ExtensionManager::getInstance(\''.$vars['FOLDER_NAME'].'\');';
	$vars['CONTENT_VARS'][] = 'protected $driver;';
	$vars['CONTENT_CONSTRUCTOR'][] = '$this->_driver = ExtensionManager::getInstance(\''.$vars['FOLDER_NAME'].'\');';
}

$vars['FIELD_VARS'] = implode("\n\t", $vars['FIELD_VARS']);
$vars['FIELD_CONSTRUCTOR'] = implode("\n\t\t", $vars['FIELD_CONSTRUCTOR']);
$vars['CONTENT_VARS'] = implode("\n\t", $vars['CONTENT_VARS']);
$vars['CONTENT_CONSTRUCTOR'] = implode("\n\t\t", $vars['CONTENT_CONSTRUCTOR']);

// Check if include_assets is set:
if(isset($vars['INCLUDE_ASSETS']))
{
	if(!isset($_REQUEST['delegate']['InitaliseAdminPageHead'])) {
		$_REQUEST['delegate']['InitaliseAdminPageHead'] = '/backend/';
	}
}

// Check if demo preferences should be created:
if(isset($vars['INCLUDE_PREFERENCES']))
{
	if(!isset($_REQUEST['delegate']['AddCustomPreferenceFieldsets'])) {
		$_REQUEST['delegate']['AddCustomPreferenceFieldsets'] = '/system/preferences/';
	}
	if(!isset($_REQUEST['delegate']['Save'])) {
		$_REQUEST['delegate']['Save'] = '/system/preferences/';
	}
}

// Check if simple XSL function should be added for field:
if(isset($vars['FIELD_PARSE_XSL']))
{
	$vars['FIELD_PARSE_XSL'] = '/**
	 * Little helper function to make use of XSL and XML in your extensions.
	 * @param $xsl
	 * @param $xml
	 * @return string
	 */
	private function parseXSL($xsl, $xml) {
		$xslt = new XSLTProcessor();
		$xslt->importStylesheet(new  SimpleXMLElement($xsl));
		return $xslt->transformToXml(new SimpleXMLElement($xml));
	}
	';
	$vars['FIELD_PUBLISH_FUNCTION'] = '// The XML Element with all the data which the XSL can use:
		$xml = new XMLElement(\'data\');
		$xml->appendChild(new XMLElement(\'field\', null, array(
			\'name\' => \'fields\'.$fieldnamePrefix.\'[\'.$this->get(\'element_name\').\']\'.$fieldnamePostfix,
			\'value\'=> $value
		)));

		// Create a div with the content of the parsed XSL file:
		$label->appendChild(
			new XMLElement(\'div\', $this->parseXSL(
					file_get_contents(EXTENSIONS.\'/'.$vars['FOLDER_NAME'].'/fields/publish.xsl\'),
					$xml->generate()
				)
			)
		);
		';
} else {
	$vars['FIELD_PARSE_XSL'] = '';
	$vars['FIELD_PUBLISH_FUNCTION'] = '$label->appendChild(Widget::Input(\'fields\'.$fieldnamePrefix.\'[\'.$this->get(\'element_name\').\']\'.$fieldnamePostfix, (strlen($value) != 0 ? $value : NULL)));';
}

// Functions, according to chosen delegates:
$vars['DELEGATES_ARRAY'] = array();
$vars['DELEGATES_FUNCTIONS'] = array();
if(isset($_REQUEST['delegate']))
{
	foreach($_REQUEST['delegate'] as $name => $context)
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

		if(isset($vars['INCLUDE_PREFERENCES']) && $name == 'AddCustomPreferenceFieldsets')
		{
			$code .= '// Create preference group
		$group = new XMLElement(\'fieldset\');
		$group->setAttribute(\'class\', \'settings\');
		$group->appendChild(new XMLElement(\'legend\', __(\''.$vars['NAME'].'\')));

		$label = Widget::Label(__(\'Example #1\'));
		$label->appendChild(Widget::Input(\'settings['.$vars['FOLDER_NAME'].'][example-1]\',
			Symphony::Configuration()->get(\'example-1\', \''.$vars['FOLDER_NAME'].'\')));
		$group->appendChild($label);

		$label = Widget::Label();
		$value = Symphony::Configuration()->get(\'example-2\', \''.$vars['FOLDER_NAME'].'\');
		if(empty($value)) { $value = \'yes\'; }
		$input = Widget::Input(\'settings['.$vars['FOLDER_NAME'].'][example-2]\', \'yes\' , \'checkbox\', ($value == \'yes\' ? array(\'checked\'=>\'checked\') : null));
		$label->setValue($input->generate() . \' \' . __(\'Example #2\'));
		$group->appendChild($label);

		// Append help
		$group->appendChild(new XMLElement(\'p\', __(\'Hello world!\'), array(\'class\' => \'help\')));

		// Append new preference group
		$context[\'wrapper\']->appendChild($group);

		';
		}

		if(isset($vars['INCLUDE_PREFERENCES']) && $name == 'Save')
		{
			$code .= '// Save the configuration
		$data = $context[\'settings\'][\''.$vars['FOLDER_NAME'].'\'];
		if(!isset($data[\'example-2\'])) { $data[\'example-2\'] = \'no\'; }
		foreach($data as $key => $value)
		{
			Symphony::Configuration()->set($key, $value, \''.$vars['FOLDER_NAME'].'\');
		}
		if(version_compare(Administration::Configuration()->get(\'version\', \'symphony\'), \'2.2.5\', \'>\'))
		{
			// S2.3+
			Symphony::Configuration()->write();
		} else {
			// S2.2.5-
			Administration::instance()->saveConfig();
		}
		';
		}

		$vars['DELEGATES_FUNCTIONS'][] = "\t".'/*
		 * Delegate \''.$name.'\' function
		 * @param $context
		 *  Provides the following parameters:
	'.$parameters.'	 */
		public function action'.$name.'($context) {
			'.$code.'// Your code goes here...
		}
		';
	}
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
copyFiles('tpl/*', 'export/'.$vars['FOLDER_NAME'], $vars);
if(isset($vars['INCLUDE_ASSETS'])) { copyFiles('tpl/assets/*', 'export/'.$vars['FOLDER_NAME'].'/assets', $vars); }

// Fields:
if($vars['TYPE'] == 'Field') { copyFiles('tpl/fields/*', 'export/'.$vars['FOLDER_NAME'].'/fields', $vars); }
if(!isset($_REQUEST['vars']['field_parse_xsl'])) {
	if(file_exists('export/'.$vars['FOLDER_NAME'].'/fields/publish.xsl'))
	{
		unlink('export/'.$vars['FOLDER_NAME'].'/fields/publish.xsl');
	}
}

// Content:
if(isset($vars['INCLUDE_CONTENT'])) { copyFiles('tpl/content/*', 'export/'.$vars['FOLDER_NAME'].'/content', $vars); }

if(isset($_REQUEST['download']))
{
	if(class_exists('ZipArchive')) {

		// Zip that shit:
		if(file_exists('./tmp/extension.zip')) { unlink('./tmp/extension.zip'); }
		Zip('export/', './tmp/extension.zip');

		header('Content-type: application/zip');
		header('Content-Disposition: attachment; filename="extension.zip"');
		readfile('./tmp/extension.zip');
	} else {
		echo 'Your extension is created and you can download it from the export-folder. Grab it while it\'s hot!';
	}
} else {
	echo 'done';
}