<?php
/**
 * Created by Symphony Extension Developer.
 * Part of '{{NAME}}' extension.
 * {{DATE}}
 */

require_once(TOOLKIT . '/class.administrationpage.php');

class contentExtension{{CLASS_NAME}}Index extends AdministrationPage {
	{{CONTENT_VARS}}

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		{{CONTENT_CONSTRUCTOR}}
	}

	/**
	 * This function is called when building the page. Use it for example to add scripts and stylesheets to the page.
	 * @param $context
	 */
	public function build($context) {
		parent::build($context);

		// Uncomment these lines to add scripts and stylesheets to this page:
		// $this->addStylesheetToHead(URL.'/extensions/{{FOLDER_NAME}}/assets/...', 'screen');
		// $this->addScriptToHead(URL.'/extensions/{{FOLDER_NAME}}/assets/...');
	}

	/**
	 * This function is called when the index-page is called.
	 * The URL for this page is /symphony/extension/{{FOLDER_NAME}}/index/index
	 */
	public function __viewIndex() {
		$this->Context->appendChild(
			new XMLElement('h2', 'Hello World!')
		);

		// To add XML Elements to the form use $this->Form->appendChild(...)
	}

	/**
	 * This function is called when the new-page is called
	 * The URL for this page is /symphony/extension/{{FOLDER_NAME}}/index/new
	 */
	public function __viewNew() {
		$this->Context->appendChild(
			new XMLElement('h2', 'New')
		);
	}

	/**
	 * This function is called when the edit-page is called
	 * The URL for this page is /symphony/extension/{{FOLDER_NAME}}/index/edit
	 */
	public function __viewEdit() {
		$this->Context->appendChild(
			new XMLElement('h2', 'Edit')
		);
	}

	/**
	 * This function is called when $_REQUEST['action'] is set on a call to the index page.
	 */
	public function __actionIndex() {
		// Your code goes here...

		// Redirect back to the page:
		redirect($_SERVER['REQUEST_URI']);
	}

	/**
	 * This function is called when $_REQUEST['action'] is set on a call to the new page.
	 */
	public function __actionNew() {
		// Your code goes here...

		// Redirect back to the page:
		redirect($_SERVER['REQUEST_URI']);
	}

	/**
	 * This function is called when $_REQUEST['action'] is set on a call to the edit page.
	 */
	public function __actionEdit() {
		// Your code goes here...

		// Redirect back to the page:
		redirect($_SERVER['REQUEST_URI']);
	}

}
