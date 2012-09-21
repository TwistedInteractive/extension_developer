<?php
/**
 * Created by Symphony Extension Developer.
 * {{DATE}}
 */

class Extension_{{CLASS_NAME}} extends Extension
{


	/**
	 * About information
	 */
	public function about()
	{

	}

	/**
	 * Get the subscribed delegates
	 * @return array
	 */
	public function getSubscribedDelegates()
	{
		return array(
			{{DELEGATES}}
		);
	}

	{{DELEGATE_FUNCTIONS}}

	/**
	 * Installation instructions
	 */
	public function install()
	{

	}

	/**
	 * Uninstall instructions
	 */
	public function uninstall()
	{

	}

	/**
	 * Update instructions
	 * @param $previousVersion
	 *  The version that is currently installed in this Symphony installation
	 */
	public function update($previousVersion) {

	}

}