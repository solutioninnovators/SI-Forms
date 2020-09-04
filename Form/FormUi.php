<?php namespace ProcessWire;
/**
 * Class FormUi
 * @version 1.1.14
 *
 * FormUI is the base class for forms. It holds a collection of fields and the logic for looping through the collection to validate the form as a whole.
 *
 * Note: HTML5 allows fields outside of a form tag to still be associated with the form. Just add the attribute form="my_form_id" to the field you create
 *
 * @todo: When setting init values, should we have an option to use the field's saveField value to populate it with the database value automatically? We could add a getDbValue and getDbValueCallback
 * @todo: Possibly create a parent class that both FormUi and FieldUi inherit from.
 * @todo: Also add option to save individual fields that are valid (during validation) even when not all fields are valid?
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
	public $savePage; // Optional ProcessWire page field to automatically save values to on a successful form submission (used for fields where $savePage is not set on the field itself)
	public $validateCallback; // Additional validation to run after the individual fields are validated and before the success callback. Should return true|false. You may also return an error string.
	public $successCallback; // Callback method to process the form data after successful validation
	public $errorCallback; // Callback to run if the validation was unsuccessful
	public $novalidate = true; // Disable the browser's built in validation on HTML5 inputs
	public $legacyMode = false; // Allows older implementations of SI Form (prior to the use of the successCallback and errorCallback) to function properly without having to make any changes to the implementation
	public $sessionValuesPulled = false; // For use with $legacyMode only
	public $alwaysProcess = false; // Should the form undergo validation/processing every time it is rendered, even if the user didn't submit it? For example, a search form that should perform the search on every page load, regardless of whether the user submitted it.

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

			if(isset($_SESSION['Session']['forms'][$this->id][$field->name])) {
				$field->value = $_SESSION['Session']['forms'][$this->id][$field->name];
			}

			$field->afterValueSet();
		}
	}

	/**
	 * Pulls values from post or get
	 *
	 * @param boolean $ignoreBlankValues - If set to true, the value will only be pulled if it is a value that would render the field "populated". Otherwise, the default value will be used. This was originally used when reloading fields with dependencies, but that resulted in the inibility to set a field with dependencies to be blank and have the dependent fields respond appropriately, so it is no longer in use anywhere. I believe that excludedFields now covers the original use cases without the side-effects of ignoreBlankValues.
	 *
	 * @param array $excludedFields - Optional array of field names to exclude pulling input values for. This is used for dependent fields who's values should be reset when they are reloaded, instead of preserving the current value on the screen, whether that be the default value or the value a user entered.
	 *
	 */
	private function pullInputValues($ignoreBlankValues = false, $excludedFields = []) {
		foreach($this->children as $field) {
			if(!$field instanceof FieldUi) continue;

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

				$initValue = $field->value;
				$field->value = $rawInputValue; // Set the field's value attribute to the value from post or get

				// If we're ignoring blank values and the field would now be considered unpopulated, or if we are explicitly excluding it, reset the field value back to the initial/default value and process its callback if necessary
				if(($ignoreBlankValues && !$field->isPopulated()) || in_array($field->name, $excludedFields)) {
					$field->value = $initValue;
					$this->setInitValues([$field]); // This includes a call to afterValueSet()
				}
				else {
					$field->afterValueSet();
				}

				// Save the value to the session (if session storage is on)
				if($this->sessionStorage) {
					$_SESSION['Session']['forms'][$this->id][$field->name] = $field->value;
				}
			}
			else { // If there was no value for this field in post or get, we need to pull the default value for it
				$this->setInitValues([$field]);
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
		if(!$this->legacyMode) {
			if($this->isSubmission()) { // Hidden field is used to identify the form was submitted
				$this->processSubmit();
			}
			else { // If not a submission, populate the field with either the session or the initial values
				$this->processNonSubmit();
			}
		}
		
		return [
			'fieldsOut' => $this->renderFields() // Render the html for the fields
		];
	}

	/**
	 * Set the default values for each field by running their callback functions, if present (e.g. to populate them with their current values from the database)
	 *
	 * @param array $fields - An array of the fields you want to set values for. Leave blank to set values for all fields in the form.
	 */
	protected function setInitValues(array $fields = null) {
		if($fields === null) {
			$fields = $this->children;
		}

		foreach($fields as $field) {
			if(!$field instanceof FieldUi) continue;

			if(self::isCallback($field->value)) {
				$field->value = call_user_func_array($field->value, [$field, $this]);
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
			return $this->findField($this->children, $name);
		}
		else return $this->children;
	}

	/**
	 * Returns all fields and subfields in a flattened array
	 */
	private function allFields() {
		$allFieldsArray = [];

		function recurseFields($fields, &$allFieldsArray) {
			foreach($fields as $field) {
				if(is_array($field)) { // Repeaters with multiple fields per row
					foreach($field as $f) {
						$allFieldsArray[] = $f;
					}
				}
				else {
					$allFieldsArray[] = $field;
				}
				if(isset($field->children) && is_array($field->children) && count($field->children)) {
					recurseFields($field->children, $allFieldsArray);
				}
			}
		}

		recurseFields($this->children, $allFieldsArray);

		return $allFieldsArray;
	}

	/**
	 * Internal method that recursively does the searching for {@link FormUI::fields} function
	 *
	 * @param array $fields
	 * @param string $name
	 * @return FieldUI
	 */
	private function findField(array $fields, string $name) {
		foreach($fields as $field) {
			if(is_array($field)) { // Repeaters with multiple fields per row
				foreach($field as $f) {
					if($f->name == $name) {
						return $f;
					}
				}
			}
			elseif($field->name == $name) {
				return $field;
			}
			elseif(isset($field->children) && is_array($field->children)) {
				// Check sub-groups if they have the field we are looking for
				$result = $this->findField($field->children, $name);
				if($result !== null) {
					return $result;
				}
			}
		}
		return null;
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

		return $this->processForm();
	}

	protected function processForm() {
		if($this->validate()) {
			// Save any fields that are configured to save automatically
			$this->saveForm();

			// If a success callback function was specified, call it now
			if(self::isCallback($this->successCallback)) {
				call_user_func_array($this->successCallback, [$this]);
			}

			return true;
		}
		else {
			if(self::isCallback($this->errorCallback)) {
				call_user_func_array($this->errorCallback, [$this]);
			}

			return false;
		}
	}

	/**
	 * Saves any fields on the form that have a savePage or a saveCallback defined, or if the form itself has a savePage defined.
	 */
	protected function saveForm() {
		// If the form or fields have a savePage defined, or the fields have a saveCallback defined, go ahead and try to save them automatically
		foreach($this->children as $field) {
			if(($this->savePage && $field->savePage !== false) || $field->savePage || $field->saveCallback) {
				$field->save();
			}
		}

		// If there is a savePage set on the form itself, we call save once at the end, for better efficiency.
		if($this->savePage) {
			$of = $this->savePage->of(false);
			$this->savePage->save();
			$this->savePage->of($of);
		}
	}

	protected function processNonSubmit() {
		if($this->sessionStorage) {
			$this->pullSessionValues();
		}
		else {
			$this->setInitValues();
		}

		if($this->alwaysProcess) {
			$this->processForm();
		}
	}

	protected function ajax_submit() {
		if(!$this->ajaxSubmit) throw new WireException('ajaxSubmit is not enabled for this form');

		$data = [];
		$data['fieldData'] = [];

		$result = $this->processSubmit();
		$allFields = $this->allFields();

        if($result) {
			$data['success'] = 1;
		}
		else {
			// Return any errors found in the validation process
			$data['error'] = $this->error;

			foreach($allFields as $field) {
				if($field->error) {
					$data['fieldData'][$field->name]['error'] = $field->error;
				}
			}
		}

        foreach($allFields as $field) {
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
		// For the sake of field dependencies, we need to set the values for all of the fields in the form
		$this->pullInputValues();

		$field = $this->fields($this->input->post->field);
		if(is_array($field) || is_null($field)) {
			$invalidFieldName = $this->wire('sanitizer')->entities($this->input->post->field);
			throw new WireException("Field could not be validated and/or saved via ajax. The \"field\" parameter is missing or invalid. Invalid value: $invalidFieldName");
		}

		if(!$field->ajaxValidate && !$field->ajaxSave) {
			throw new WireException("Ajax is not enabled for $field->name");
		}

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
			if($field->save(true)) {
				$data['saved'] = 1;
			}
			else {
				$data['error'] = __('Save failed: Input is valid but field could not be saved.');
			}
		}

		return $data;
	}

	/**
	 * Reload one or more fields in the form via ajax to update its view
	 */
	protected function ajax_reloadFields($input) {
		$fieldNames = $this->sanitizer->array($input['fieldNames'], 'text');

		// For the sake of field dependencies and to preserve the current user-provided values for the fields we're reloading, we need to set the values for all of the fields in the form first. If the resetValueOnReload property is set, we want to exclude them so that their default values will take precedence over the user-submitted values.
		$excludedFields = [];
		foreach($fieldNames as $fieldName) {
			$field = $this->fields($fieldName);

			if($field->resetValueOnReload) {
				$excludedFields[] = $fieldName;
			}
		}
		$this->pullInputValues(false, $excludedFields);


		// Render and return the views for each field we requested
		$views = [];
		foreach($fieldNames as $fieldName) {
			$field = $this->fields($fieldName);

			if($field instanceof FieldUi) {
				$views[$fieldName] = $field->render();
			}
		}

		return [
			'views' => $views
		];

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