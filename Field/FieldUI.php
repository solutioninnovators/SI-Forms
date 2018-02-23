<?php namespace ProcessWire;
/**
 * Class FieldUI
 * @package ProcessWire
 *
 * The FieldUI class is the base for inputfields. Fields are responsible for handling and validating their own input, as well as outputting their own html markup.
 *
 * Basic usage (outside of a form):
 *
 * $f = new FieldUI();
 * $f->name = 'my_field';
 * $f->label = My Field
 * $f->required = true;
 * $f->value = ''; // Set default value (i.e. pull the current value from the database)
 * // Set any other properties here
 *
 * if($input->post->my_field) {
 * 		$f->value = $input->post->my_field; // Set value to input from post or get
 * 		if($f->validate()) {
 *			// If valid, write the value to the database
 * 		}
 * }
 * $f->output(); // Output the html markup
 *
 * @todo: most public properties should be set private to encourage users to pass configuration into the constructor
 *
 */
abstract class FieldUI extends UI {

	public $name = ''; // Name of the html input. If this field maps to a field on a ProcessWire Page and you're using ajaxSave, the name should be the same
	public $label = '';
	public $maxLength = '';
	public $required = false;
	public $value = '';
	public $placeholder = '';
	public $error = ''; // Holds an error if the field didn't validate
	public $icon = '';
	public $disabled = false;
	public $ajaxSave = false; // Turn on ajax save
	public $savePage; // Optional ProcessWire Page object or ID to save the field to (used for AJAX saves)
	public $saveField; // Optional ProcessWire Field name - if different from this UI's name property (used for AJAX saves)
	public $description = ''; // Text that appears before field
	public $notes = ''; // Text that appears below field
	public $inputClasses = '';
	public $readOnly = false; // Set to true to display the value of the field while preventing modification or posting to the form
	public $autofocus = false; // Set focus to this field when the page loads

	/**
	 * The validate method should evaluate $this->value and return true or false depending on whether it is a valid input for the given field. If an error is discovered, it should populate $this->error
	 * @todo: Consider making the required check a separate function in this base class so that the logic doesn't need to be repeated?
	 *
	 * @return boolean
	 */
	public function validate() {
		// Test $this->value
		$this->error = 'The validate function is undefined.';
		return false;
	}

	/**
	 * Save a single field with AJAX
	 */
	public function ajax_save() {
		$this->value = $this->input->post->value;

		$this->ajax['error'] = '';
		$this->ajax['saved'] = 0;

		// If $savePage specified as ID, convert to Page
		if($this->savePage instanceof Page) {
			$savePage = $this->savePage;
		} else {
			$savePage = $this->pages->get((int)$this->savePage);
		}

		// If no saveField specified, fallback to UI name
		if($this->saveField) {
			$saveField = $this->saveField;
		} else {
			$saveField = $this->name;
		}

		if($savePage->id) {
			// Try to validate and save the value to the page
			if($this->validate()) {
				if($savePage->setAndSave($saveField, $this->value)) {
					$this->ajax['saved'] = 1;
				}
				else {
					$this->ajax['error'] = 'Save failed: Input is valid but field could not be saved.';
				}
			}
			else {
				$this->ajax['error'] = $this->error;
			}
		}
		else {
			$this->ajax['error'] = 'Save failed: savePage is not properly configured.';
		}
	}

	/**
	 * Make sure cloning a field also clones any children it has
	 */
	/*public function __clone() {
		if(isset($this->children)) {
			$newChildren = [];
			foreach($this->children as $child) {
				$newChildren[] = clone $child;
			}
			$this->children = $newChildren;
		}
	}*/
	
}