<?php

$delegatesArr = array();

// Get the delegates from the Symphony site with some xpath and black magic:

/*
<delegate name="AddCustomPreferenceFieldsets">
	<description>
		<p>Add Extension custom preferences. Use the $wrapper reference to append objects.</p>
	</description>
	<location line="111">content/content.systempreferences.php</location>
	<parameters>
		<parameter name="context" type="string">
			<description>
				<p>'/system/preferences/'</p>
			</description>
		</parameter>
		<parameter name="wrapper" type="XMLElement">
			<description>
				<p>An XMLElement of the current page</p>
			</description>
		</parameter>
		<parameter name="errors" type="array">
			<description>
				<p>An array of errors</p>
			</description>
		</parameter>
	</parameters>
</delegate>
 */
if(is_writable('tmp'))
{
	copy('http://getsymphony.com/workspace/api/2.3/delegates.xml', 'tmp/delegates.xml');
	$xml = simplexml_load_file('tmp/delegates.xml');
} else {
	$xml = simplexml_load_file('http://getsymphony.com/workspace/api/2.3/delegates.xml');
}

/* @var $xml SimpleXMLElement */

foreach($xml->xpath('/delegates/delegate') as $delegateXML)
{
	$context = $delegateXML->xpath('parameters/parameter[@name=\'context\']/description/p');
	$arrKey  = trim(str_replace('\'', '', (string)$context[0]));
	if(!isset($delegatesArr[$arrKey])) { $delegatesArr[$arrKey] = array(); }
	if(strpos((string)$context[0], '\' or \'') !== false)
	{
		// These are actually more delegates:
		$a = explode('\' or \'', (string)$context[0]);
		foreach($a as $p)
		{
			$p = str_replace('\'', '', $p);
			if(!isset($delegatesArr[$p])) { $delegatesArr[$p] = array(); }
			$delegatesArr[$p][] = array(
				'name'   	 => (string)$delegateXML->attributes()->name,
				'page'       => trim(str_replace('\'', '', $p)),
				'parameters' => $delegateXML->xpath('parameters/parameter[not(@name=\'context\')]')
			);
		}
	} elseif(strpos((string)$context[0], '(edit|new|info)') !== false) {
		// These are actually 3 delegates:
		$arrKey = str_replace('\'', '', (string)$context[0]);
		$edit 	= str_replace('(edit|new|info)', 'edit', $arrKey);
		$new 	= str_replace('(edit|new|info)', 'new', $arrKey);
		$info 	= str_replace('(edit|new|info)', 'info', $arrKey);
		if(!isset($delegatesArr[$edit])) { $delegatesArr[$edit] = array(); }
		if(!isset($delegatesArr[$new])) { $delegatesArr[$new] = array(); }
		if(!isset($delegatesArr[$info])) { $delegatesArr[$info] = array(); }
		$delegatesArr[$edit][] = array(
			'name'   	 => (string)$delegateXML->attributes()->name,
			'page'       => trim(str_replace('\'', '', $edit)),
			'parameters' => $delegateXML->xpath('parameters/parameter[not(@name=\'context\')]')
		);
		$delegatesArr[$new][] = array(
			'name'   	 => (string)$delegateXML->attributes()->name,
			'page'       => trim(str_replace('\'', '', $new)),
			'parameters' => $delegateXML->xpath('parameters/parameter[not(@name=\'context\')]')
		);
		$delegatesArr[$info][] = array(
			'name'   	 => (string)$delegateXML->attributes()->name,
			'page'       => trim(str_replace('\'', '', $info)),
			'parameters' => $delegateXML->xpath('parameters/parameter[not(@name=\'context\')]')
		);
	} else {
		// Regular delegate:
		$delegatesArr[$arrKey][] = array(
			'name'   	 => (string)$delegateXML->attributes()->name,
			'page'       => trim(str_replace('\'', '', (string)$context[0])),
			'parameters' => $delegateXML->xpath('parameters/parameter[not(@name=\'context\')]')
		);
	}
}

ksort($delegatesArr);

// Old style:
/*$dom = new DOMDocument();
@$dom->loadHTMLFile('http://getsymphony.com/learn/api/2.3/delegates/');

$xpath = new DOMXPath($dom);

$sections = $xpath->query('//div[@class=\'collapsible collapse\']');

foreach($sections as $section)
{
	$sectionArr = array('name' => $xpath->query('h4/text()', $section)->item(0)->textContent, 'items' => array());

	$delegates = $xpath->query('div[@class=\'docblock\']', $section);

	foreach($delegates as $delegate)
	{
		$page = trim(str_replace(array('$page=\'', '\',', '\')'), '', $xpath->query('h5/code/text()', $delegate)->item(2)->textContent));
		if(strpos($page, '\' or \'') !== false)
		{
			// These are actually more delegates:
			$a = explode('\' or \'', $page);
			foreach($a as $p)
			{
				$sectionArr['items'][] = array('name' => $delegate->getAttribute('id'), 'page'=>trim($p));
			}
		} elseif(strpos($page, '(edit|new|info)') !== false) {
			// These are actually 3 delegates:
			$edit 	= str_replace('(edit|new|info)', 'edit', $page);
			$new 	= str_replace('(edit|new|info)', 'new', $page);
			$info 	= str_replace('(edit|new|info)', 'info', $page);
			$sectionArr['items'][] = array('name' => $delegate->getAttribute('id'), 'page'=>$edit);
			$sectionArr['items'][] = array('name' => $delegate->getAttribute('id'), 'page'=>$new);
			$sectionArr['items'][] = array('name' => $delegate->getAttribute('id'), 'page'=>$info);
		}else {
			// Everything is ok:
			$sectionArr['items'][] = array('name' => $delegate->getAttribute('id'), 'page'=>$page);
		}

	}

	$delegatesArr[] = $sectionArr;

}*/
