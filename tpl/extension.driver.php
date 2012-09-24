<?php
/**
 * Created by Symphony Extension Developer.
 * {{DATE}}
 */

class Extension_{{CLASS_NAME}} extends Extension
{

	/**
	 * About information
	 * For if you want to create a pre-2.3-extension
	 */
	public function about()
	{
		return array(
			'name'			=> '{{NAME}}',
			'version'		=> '{{VERSION}}',
			'release-date'	=> '{{DATE}}',
			'author'		=> array(
				array(
					'name' => '{{AUTHOR_NAME}}',
					'website' => '{{AUTHOR_WEBSITE}}',
					'email' => '{{AUTHOR_EMAIL}}'
				)
			)
		);
	}

	/**
	 * Get the subscribed delegates
	 * @return array
	 */
	public function getSubscribedDelegates()
	{
		return array(
{{DELEGATES_ARRAY}}
		);
	}

{{DELEGATES_FUNCTIONS}}
	/**
	 * Installation instructions
	 */
	public function install()
	{
{{INSTALL_INSTRUCTIONS}}
	}

	/**
	 * Uninstall instructions
	 */
	public function uninstall()
	{
{{UNINSTALL_INSTRUCTIONS}}
	}

	/**
	 * Update instructions
	 * @param $previousVersion
	 *  The version that is currently installed in this Symphony installation
	 */
	public function update($previousVersion) {
		if (version_compare($previousVersion, '1.1', '<')) {
			// Update from pre-1.1 to 1.1:

		}
	}

}