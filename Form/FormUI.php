<?php namespace ProcessWire;
/**
 * Class FormUI
 * @package ProcessWire
 *
 * FormUI is the base class for forms. It holds a collection of fields and the logic for looping through the collection to validate the form as a whole.
 *
 * Note: HTML5 allows fields outside of a form tag to still be associated with the form. Just add the attribute form="my_form_id" to the field you create
 *
 * @todo: Add getters and setters for the values and options of fields. This way options and values of fields can be calculated as soon as they're needed, making dependencies cleaner.
 *
 */
class FormUI extends UI {

	public $disableCSRF = false; // If using the post method, PW's CSRF protection will be used by default. Set true to disable it.
	public $id = 'form'; // Unique name for this form to distinguish from other UIs (also used for the form's session namespace when $sessionStorage = true)
	public $markup = true; // Include the <form> tags and attributes in the output returned from the UI (Recommended)
	public $formClasses = ''; // CSS classes to add to the form
	public $method = 'post'; // post or get
	public $action = ''; // URI the form will submit to. Leave blank for current page
	public $target = '_self';
	public $name = ''; // Optional form name attribute
	public $autocomplete = 'on'; // Allow the browser to autocomplete the form (on|off)
	public $sessionStorage = false; // Enable automatic storage and retrieval of field values in the session
	public $error; // Holds an error set by the form after validate() is called
	public $ajaxSubmit = false; // Should the form be submitted via ajax? (avoids refreshing the page)
	public $validateCallback; // Additional validation to run after the individual fields are validated and before the success callback. Should return true|false. You may also return an error string.
	public $successCallback; // Callback method to process the form data after successful validation
	public $errorCallback; // Callback to run if the validation was unsuccessful
	public $novalidate = true; // Disable the browser's built in validation on HTML5 inputs

	// Regions that may be populated with html
	public $beforeForm = '';
	public $afterForm = '';

	protected $children = [];  // Holds the fields

	public $version = 2;

	protected function setup() {}

	/**
	 * Make sure a session array exists for this form and create one if it doesn't
	 */
	protected function initFormSession() {
		if(!isset($_SESSION['Session']['forms'])) $_SESSION['Session']['forms'] = [];
		if(!isset($_SESSION['Session']['forms'][$this->id])) $_SESSION['Session']['forms'][$this->id] = [];
	}

	/**
	 * Gets the values from the session and populates the form fields with them.
	 */
	protected function pullSessionValues() {
		$this->initFormSession();

		foreach($this->fields() as $field) {
			if(!$field instanceof FieldUI) continue;

			$field->runOptionCallbacks();

			if(isset($_SESSION['Session']['forms'][$this->id][$field->name])) {
				$field->value = $_SESSION['Session']['forms'][$this->id][$field->name];
			}
		}
	}

	/**
	 * Pulls values from post or get
	 *
	 */
	private function pullInputValues() {
		foreach($this->children as $field) {
			if(!$field instanceof FieldUI) continue;

			$field->runOptionCallbacks();

			// Set the field's value attribute to the value from from post or get
			if($field->type === 'file') {
				$rawInputValue = isset($_FILES[$field->name]) ? $_FILES[$field->name] : null;
			}
			elseif($this->method === 'get') {
				$rawInputValue = isset($_GET[$field->name]) ? $_GET[$field->name] : null;
			}
			elseif($this->method === 'post') {
				$rawInputValue = isset($_POST[$field->name]) ? $_POST[$field->name] : null;
			}

			// @todo: Include this in our refactored Field::getValue in the future
			if($rawInputValue !== null) { // Only pull vars that actually exist in post or get (the field existed on the page when it was submitted)

				// Convert the strings '#' and '[#]' to empty string and empty array, respectively.
				// These are the values provided by our hidden fallback/dummy fields for html inputs that do not submit values when they are present but empty. This allows us to differentiate between a field that was not submitted and one that was intentionally blank/empty
				if($rawInputValue === '#') {
					$rawInputValue = '';
				}
				elseif($rawInputValue === '[#]') {
					$rawInputValue = [];
				}

				$field->value = $rawInputValue; // Set the field's value attribute to the value from post or get

				// Save the value to the session (if session storage is on)
				if($this->sessionStorage) {
					$_SESSION['Session']['forms'][$this->id][$field->name] = $rawInputValue;
				}
			}
		}
	}

	/**
	 * Call to remove all values for this form from the session. For example, after saving values to the database.
	 */
	public function clearSessionValues() {
		$this->initFormSession();

		unset($_SESSION['Session']['forms'][$this->id]);
	}

	/**
	 * Is the current page request/response a form submit that includes this form?
	 */
	public function isSubmission() {
		$hiddenField = "form_{$this->id}";
		if($this->input->{$this->method}->$hiddenField) { // A hidden field is used to identify that the form was submitted
			return true;
		}

		return false;
	}

	protected function run() {
		// If the form was submitted, we will process it
		if($this->isSubmission()) { // Hidden field is used to identify the form was submitted
			$this->processSubmit();
		}
		else { // If not a submission, populate the field with either the session or the initial values
			$this->processNonSubmit();
		}

		$this->view->fieldsOut = $this->outputFields(); // Render the html for the fields
	}

	/**
	 * Set the default values for each field by running their callback functions, if present (e.g. to populate them with their current values from the database)
	 */
	protected function setInitValues() {
		foreach($this->children as $field) {
			if(!$field instanceof FieldUI) continue;

			$field->runOptionCallbacks();

			if (is_callable($field->value)) {
				$field->value = call_user_func_array($field->value, [$field]);
			}
		}
	}

	/*
	protected function runOptionCallbacks() {
		foreach($this->children as $field) {
			if(!$field instanceof FieldUI) continue;

			$field->runOptionCallbacks();
		}
	}
	*/

	/**
	 * Gets values from post or get, sets the value for each field's $value attribute, and calls validate() on each field. Sets a global error message to $this->error. These messages may be overridden by the client code if desired.
	 *
	 * @return bool true if all fields are valid, false if any are invalid.
	 *
	 */
	protected function validate() {
		$valid = true;

		if($this->method == 'get' || $this->disableCSRF || $this->session->CSRF->hasValidToken()) {

			// Validate fields
			foreach($this->children as $field) {
				if(!$field instanceof FieldUI) continue;

				if(!$field->validate()) $valid = false; // Run the field's validate function
			}

			// Run custom validateCallback for entire form (if one exists)
			if(is_callable($this->validateCallback)) {
				$callbackResult = call_user_func_array($this->validateCallback, [$this]);

				if(is_string($callbackResult)) {
					$this->error = $callbackResult;
					$valid = false;
				}
				elseif(!$callbackResult) {
					$valid = false;
				}
			}

			if(!$valid) {
				$this->error = "Please fix the errors indicated below and resubmit the form.";
			}

		}
		else {
			$valid = false;
			$this->error = "We've detected a possible security issue (CSRF) with your submission. Just to be safe, please try submitting the form again.";
		}

		//if($this->error) $this->session->error = $this->error;

		return $valid;
	}

	/**
	 * @param FieldUI $field Adds a field to the form
	 */
	public function addField(UI $field) {
		$field->setForm($this); // Inject the form reference into the field
		$this->children[] = $field;
	}

	/**
     * @param FieldUI $field Removes a field from the form
     */
    public function removeField(FieldUI $field) {
        for($i = count($this->children)-1; $i >= 0; $i--) {
            $f = $this->children[$i];
            if($f == $field) {
                array_splice($this->children,$i,1);
            }
        }
    }

	/**
	 * @param $name - Name of field to return
	 * @return Returns all fields or a single field matching the given name
	 */
	public function fields($name = null) {
		if($name) {
			foreach($this->children as $field) {
				if($field->name == $name) return $field;
			}
			return null;
		}
		else return $this->children;
	}

	/**
	 * Loops through and concatenates output of all fields in this form
	 */
	public function outputFields() {
		$fieldsOut = '';
		foreach($this->fields() as $field) {
			$fieldsOut .= $field->output();
		}
		return $fieldsOut;
	}

	protected function processSubmit() {
		$this->pullInputValues(); // Get the submitted values from post or get

		if($this->validate()) {
			// If the fields have a savePage defined, go ahead and try to save them automatically
			// @todo: Also add option to save individual fields that are valid (during validation) even when not all fields are valid?
			foreach($this->children as $field) {
				if($field->savePage || $field->saveCallback) { //@todo: delegate this to fieldui?
					$field->save();
				}
			}

			// If a callback function was specified, call it now
			if(is_callable($this->successCallback)) {
				call_user_func_array($this->successCallback, [$this]);
			}

			return true;
		}
		else {
			if(is_callable($this->errorCallback)) {
				call_user_func_array($this->errorCallback, [$this]);
			}
		}

		return false;
	}

	protected function processNonSubmit() {
		if($this->sessionStorage) {
			$this->pullSessionValues();
		}
		else {
			$this->setInitValues();
		}
	}

	protected function ajax_submit() {
		if(!$this->ajaxSubmit) throw new WireException('ajaxSubmit is not enabled for this form');

        $this->ajax['fieldData'] = [];

        if($this->processSubmit()) {
			$this->ajax['success'] = 1;
		}
		else {
			// Return any errors found in the validation process
            $this->ajax['error'] = $this->error ?: 1;

			foreach($this->children as $field) {
				if($field->error) {
					$this->ajax['fieldData'][$field->name]['error'] = $field->error;
				}
			}
		}

        foreach($this->children as $field) {
            if($field->message) {
                $this->ajax['fieldData'][$field->name]['message'] = $field->message;
            }
        }

		$this->ajax['csrfName'] = $this->session->CSRF->getTokenName();
		$this->ajax['csrfValue'] = $this->session->CSRF->getTokenValue();
	}

	/**
	 * Validates (and optionally saves) a single field in a form, in the larger context of the form (taking field dependencies into account)
	 *
	 * @return array
	 * @throws WireException
	 */
	protected function ajax_submitField() {
		$field = $this->fields($this->input->post->field);
		if(is_array($field) || is_null($field)) {
			$invalidFieldName = $this->wire('sanitizer')->text($this->input->post->field);
			throw new WireException("Field could not be validated and/or saved via ajax. The \"field\" parameter is missing or invalid. Invalid value: $invalidFieldName");
		}

		if(!$field->ajaxValidate && !$field->ajaxSave) {
			throw new WireException("Ajax is not enabled for $field->name");
		}

		// For the sake of field dependencies, we need to set the values and options for all of the fields in the form
		$this->pullInputValues();

		// Now we can validate the one field we're interested in
		if($field->validate()) {
			$this->ajax['success'] = 1;
			$valid = true;
		}
		else {
			$this->ajax['error'] = $field->error;
			$valid = false;
		}

		if($field->message) {
			$this->ajax['message'] = $field->message;
		}

		if($this->input->post->timestamp) {
			$this->ajax['timestamp'] = $this->input->post->timestamp;
		}

		// If ajaxSave is enabled, go ahead and save the field
		if($valid && $field->ajaxSave) {
			if($field->save()) {
				$this->ajax['saved'] = 1;
			}
			else {
				$this->ajax['error'] = 'Save failed: Input is valid but field could not be saved.';
			}
		}
	}
}