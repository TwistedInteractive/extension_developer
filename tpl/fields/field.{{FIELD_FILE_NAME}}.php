<?php
/**
 * Created by Symphony Extension Developer.
 * Part of '{{NAME}}' extension.
 * {{DATE}}
 */

if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

Class Field{{FIELD_CLASS_NAME}} extends Field
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_name = __('{{FIELD_NAME}}');

		// Set defaults:
		$this->set('show_column', '{{FIELD_DEFAULT_SHOW_COLUMN}}');
		$this->set('required', '{{FIELD_DEFAULT_REQUIRED}}');
		$this->set('location', '{{FIELD_DEFAULT_LOCATION}}');
	}

	/**
	 * Creation of the data table:
	 * @return mixed
	 */
	public function createTable()
	{
		return Symphony::Database()->query("
			CREATE TABLE IF NOT EXISTS `tbl_entries_data_" . $this->get('id') . "` (
			  `id` int(11) unsigned NOT NULL auto_increment,
			  `entry_id` int(11) unsigned NOT NULL{{FIELD_DATA_SQL}},
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
	}

}
