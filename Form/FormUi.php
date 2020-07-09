<?php namespace ProcessWire;
/**
 * Class FormUi
 * @version 1.0.1
 *
 * FormUI is the base class for forms. It holds a collection of fields and the logic for looping through the collection to validate the form as a whole.
 *
 * Note: HTML5 allows fields outside of a form tag to still be associated with the form. Just add the attribute form="my_form_id" to the field you create
 *
 * @todo: When setting init values, should we use the field's saveField value to populate it with the database value automatically?
 *
 */
class FormUi extends Ui {

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
	public $legacyMode = false; // Allows older implementations of SI Form (prior to the use of the successCallback and errorCallback) to function properly without having to make any changes to the implementation
	public $sessionValuesPulled = false; // For use with $legacyMode only

	// Regions inside the form tags that may be populated with html
	public $beforeForm = '';
	public $afterForm = '';

	protected $children = [];  // Holds the fields


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
			if(!$field instanceof FieldUi) continue;

			if(!$this->legacyMode) $field->runOptionCallbacks();

			if(isset($_SESSION['Session']['forms'][$this->id][$field->name])) {
				$field->value = $_SESSION['Session']['forms'][$this->id][$field->name];
			}

			$field->afterValueSet();
		}
	}

	/**
	 * Pulls values from post or get
	 *
	 */
	private function pullInputValues() {
		foreach($this->children as $field) {
			if(!$field instanceof FieldUi) continue;

			if(!$this->legacyMode) $field->runOptionCallbacks();

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

			$field->afterValueSet();
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
		if(!$this->legacyMode) {
			if($this->isSubmission()) { // Hidden field is used to identify the form was submitted
				$this->processSubmit();
			}
			else { // If not a submission, populate the field with either the session or the initial values
				$this->processNonSubmit();
			}
		}

		$this->view->fieldsOut = $this->renderFields(); // Render the html for the fields
	}

	/**
	 * Set the default values for each field by running their callback functions, if present (e.g. to populate them with their current values from the database)
	 */
	protected function setInitValues() {
		foreach($this->children as $field) {
			if(!$field instanceof FieldUi) continue;

			$field->runOptionCallbacks();

			if(self::isCallback($field->value)) {
				$field->value = call_user_func_array($field->value, [$field]);
			}

			$field->afterValueSet();
		}
	}

	/**
	 * Gets values from post or get, sets the value for each field's $value attribute, and calls validate() on each field. Sets a global error message to $this->error. These messages may be overridden by the client code if desired.
	 *
	 * This method is public for purposes of supporting $this->legacyMode. It should only be called if legacy mode is being used.
	 *
	 * @param bool $pullInputValues - Left in for legacy support @deprecated
	 * @return bool true if all fields are valid, false if any are invalid.
	 *
	 */
	public function validate($pullInputValues = true) {
		$valid = true;

		if($this->legacyMode && $this->sessionValuesPulled === false) $this->pullSessionValues();

		if($this->method == 'get' || $this->disableCSRF || $this->session->CSRF->hasValidToken()) {

			if($this->legacyMode && $pullInputValues) $this->pullInputValues();

			// Validate fields
			foreach($this->children as $field) {
				if(!$field instanceof FieldUi) continue;

				if(!$field->validate()) $valid = false; // Run the field's validate function
			}

			// Run custom validateCallback for entire form (if one exists)
			if(self::isCallback($this->validateCallback) && !$this->legacyMode) {
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
				$this->error = __("Please fix the errors indicated below and resubmit the form.");
			}
			elseif($this->legacyMode && $this->method == 'post') {
				$this->session->successMsg = __("Saved.");
			}

		}
		else {
			$valid = false;
			$this->error = __("We've detected a possible security issue (CSRF) with your submission. Just to be safe, please try submitting the form again.");
		}

		if($this->legacyMode) {
			if($this->error) $this->session->errorMsg = $this->error;
		}

		return $valid;
	}

	/**
	 * @param FieldUi $field Adds a field to the form
	 */
	public function addField(Ui $field) {
		$field->form = $this; // Inject the form reference into the field
		$this->children[] = $field;
	}

	/**
     * @param FieldUi $field Removes a field from the form
     */
    public function removeField(Ui $field) {
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
	 * Sorts fields by their index property. If the field doesn't have an index, one will be assigned. This way you can swap the position of a single field without defining an index on every single field in the form.
	 */
	public function sortFieldsByIndex() {

		// Build array of indexes that have already been defined
		$reservedIndexes = [];
		foreach($this->children as $child) {
			if(!is_null($child->index)) {
				$reservedIndexes[] = $child->index;
			}
		}

		// Set index values for the fields that weren't explicitly defined
		$index = 1;
		foreach($this->children as $child) {
			if(is_null($child->index)) {
				// Get the next index that isn't reserved
				while(in_array($index, $reservedIndexes)) {
					$index++;
				}
				$child->index = $index;
				$index++;
			}
		}

		// Sort based on the final indexes
		usort($this->children, function($a, $b) {
			return ($a->index < $b->index) ? -1 : (($a->index > $b->index) ? 1 : 0);
		});
	}

	/**
	 * Loops through and concatenates output of all fields in this form
	 */
	public function renderFields() {
		$this->sortFieldsByIndex();

		$fieldsOut = '';
		foreach($this->fields() as $field) {
			$fieldsOut .= $field->render();
		}
		return $fieldsOut;
	}

	protected function processSubmit() {
		$this->pullInputValues(); // Get the submitted values from post or get

		if($this->validate()) {
			// If the fields have a savePage defined, go ahead and try to save them automatically
			// @todo: Also add option to save individual fields that are valid (during validation) even when not all fields are valid?
			foreach($this->children as $field) {
				if($field->savePage || $field->saveCallback) {
					$field->save();
				}
			}

			// If a callback function was specified, call it now
			if(self::isCallback($this->successCallback)) {
				call_user_func_array($this->successCallback, [$this]);
			}

			return true;
		}
		else {
			if(self::isCallback($this->errorCallback)) {
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

		$data = [];

		$data['fieldData'] = [];

        if($this->processSubmit()) {
			$data['success'] = 1;
		}
		else {
			// Return any errors found in the validation process
			$data['error'] = $this->error ?: 1;

			foreach($this->children as $field) {
				if($field->error) {
					$data['fieldData'][$field->name]['error'] = $field->error;
				}
			}
		}

        foreach($this->children as $field) {
            if($field->message) {
				$data['fieldData'][$field->name]['message'] = $field->message;
            }
        }

		$data['csrfName'] = $this->session->CSRF->getTokenName();
		$data['csrfValue'] = $this->session->CSRF->getTokenValue();

		return $data;
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
			$invalidFieldName = $this->wire('sanitizer')->entities($this->input->post->field);
			throw new WireException("Field could not be validated and/or saved via ajax. The \"field\" parameter is missing or invalid. Invalid value: $invalidFieldName");
		}

		if(!$field->ajaxValidate && !$field->ajaxSave) {
			throw new WireException("Ajax is not enabled for $field->name");
		}

		// For the sake of field dependencies, we need to set the values and options for all of the fields in the form
		$this->pullInputValues();

		$data = [];

		// Now we can validate the one field we're interested in
		if($field->validate()) {
			$data['success'] = 1;
			$valid = true;
		}
		else {
			$data['error'] = $field->error;
			$valid = false;
		}

		if($field->message) {
			$data['message'] = $field->message;
		}

		if($this->input->post->timestamp) {
			$data['timestamp'] = $this->input->post->timestamp;
		}

		// If ajaxSave is enabled, go ahead and save the field
		if($valid && $field->ajaxSave) {
			if($field->save()) {
				$data['saved'] = 1;
			}
			else {
				$data['error'] = __('Save failed: Input is valid but field could not be saved.');
			}
		}

		return $data;
	}

	/**
	 * Custom method for checking if a variable can be called as a function. Differs from is_callable in that it excludes function name strings and callable objects that are not functions.
	 *
	 * @param $callback
	 * @return bool
	 */
	public static function isCallback($callback) {
		if($callback instanceof \Closure || (is_array($callback) && is_callable($callback))) {
			return true;
		}
		else {
			return false;
		}
	}
}