<?php
/**
 * Created by Symphony Extension Developer.
 * Part of '{{NAME}}' extension.
 * {{DATE}}
 */

if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

Class Field{{FIELD_CLASS_NAME}} extends Field
{
	{{FIELD_VARS}}

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

		{{FIELD_CONSTRUCTOR}}
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

	/**
	 * Display the publish panel for this field. The display panel is the
	 * interface shown to Authors that allow them to input data into this
	 * field for an `Entry`.
	 *
	 * @param XMLElement $wrapper
	 *	the XML element to append the html defined user interface to this
	 *	field.
	 * @param array $data (optional)
	 *	any existing data that has been supplied for this field instance.
	 *	this is encoded as an array of columns, each column maps to an
	 *	array of row indexes to the contents of that column. this defaults
	 *	to null.
	 * @param mixed $flagWithError (optional)
	 *	flag with error defaults to null.
	 * @param string $fieldnamePrefix (optional)
	 *	the string to be prepended to the display of the name of this field.
	 *	this defaults to null.
	 * @param string $fieldnamePostfix (optional)
	 *	the string to be appended to the display of the name of this field.
	 *	this defaults to null.
	 * @param integer $entry_id (optional)
	 *	the entry id of this field. this defaults to null.
	 */
	public function displayPublishPanel(XMLElement &$wrapper, $data = null, $flagWithError = null, $fieldnamePrefix = null, $fieldnamePostfix = null, $entry_id = null)
	{
		// Assuming your entry has a 'value'-column in it's data table:
		$value = General::sanitize($data['value']);

		$label = Widget::Label($this->get('label'));
		if($this->get('required') != 'yes') $label->appendChild(new XMLElement('i', __('Optional')));

		{{FIELD_PUBLISH_FUNCTION}}

		if($flagWithError != NULL) $wrapper->appendChild(Widget::Error($label, $flagWithError));
		else $wrapper->appendChild($label);
	}

	/**
	 * Process the raw field data.
	 *
	 * @param mixed $data
	 *	post data from the entry form
	 * @param integer $status
	 *	the status code resultant from processing the data.
	 * @param string $message
	 *	the place to set any generated error message. any previous value for
	 *	this variable will be overwritten.
	 * @param boolean $simulate (optional)
	 *	true if this will tell the CF's to simulate data creation, false
	 *	otherwise. this defaults to false. this is important if clients
	 *	will be deleting or adding data outside of the main entry object
	 *	commit function.
	 * @param mixed $entry_id (optional)
	 *	the current entry. defaults to null.
	 * @return array
	 *	the processed field data.
	 */
	public function processRawFieldData($data, &$status, &$message=null, $simulate=false, $entry_id=null)
	{
		$status = self::__OK__;

		// Assuming your entry has a 'value'-column in it's data table:
		return array(
			'value' => $data,
		);
	}

	/**
	 * Append the formatted XML output of this field as utilized as a data source.
	 *
	 * @param XMLElement $wrapper
	 *	the XML element to append the XML representation of this to.
	 * @param array $data
	 *	the current set of values for this field. the values are structured as
	 *	for displayPublishPanel.
	 * @param boolean $encode (optional)
	 *	flag as to whether this should be html encoded prior to output. this
	 *	defaults to false.
	 * @param string $mode
	 *	 A field can provide ways to output this field's data. For instance a mode
	 *  could be 'items' or 'full' and then the function would display the data
	 *  in a different way depending on what was selected in the datasource
	 *  included elements.
	 * @param integer $entry_id (optional)
	 *	the identifier of this field entry instance. defaults to null.
	 */
	public function appendFormattedElement(XMLElement &$wrapper, $data, $encode = false, $mode = null, $entry_id = null) {
		$wrapper->appendChild(new XMLElement($this->get('element_name'), ($encode ? General::sanitize($this->prepareTableValue($data, null, $entry_id)) : $this->prepareTableValue($data, null, $entry_id))));
	}

	{{FIELD_PARSE_XSL}}
}
