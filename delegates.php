<?php

$delegatesArr = array();

// Get the delegates from the Symphony site with some xpath and black magic:
$dom = new DOMDocument();
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

}
