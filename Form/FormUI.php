<?php namespace ProcessWire;
/**
 * Class FormUI
 * @package ProcessWire
 *
 * FormUI is the base class for forms. It holds a collection of fields and the logic for looping through the collection to validate the form as a whole. It does not make any assumptions or impose any structure beyond that. The client class is responsible for detecting a submit, calling validate(), and saving data.
 *
 * Note: HTML5 allows fields outside of a form tag to still be associated with the form. Just add the attribute form="my_form_id" to the field you create
 */
class FormUI extends UI {

	public $disableCSRF = false; // If using the post method, PW's CSRF protection will be used by default. Set true to disable it.
	public $id = 'form'; // Unique name for this form to distinguish from other UIs (also used for the form's session namespace when $sessionStorage = true)
	public $markup = false; // Include the <form> tags and attributes in the output returned from the UI (Recommended)
	public $formClasses = ''; // CSS classes to add to the form
	public $method = 'post'; // post or get
	public $action = ''; // URI the form will submit to. Leave blank for current page
	public $target = '_self';
	public $name = ''; // Optional form name attribute
	public $autocomplete = 'on'; // Allow the browser to autocomplete the form (on|off)
	public $sessionStorage = false; // Enable automatic storage and retrieval of field values in the session
	public $error; // Holds an error set by the form after validate() is called

	// Regions that may be populated with html
	public $beforeForm = '';
	public $afterForm = '';

	protected $formFields = [];  // Holds the fields
	protected $sessionValuesPulled = false; // Set to true after pullSessionValues() is called

	protected function setup() {}

	/**
	 * Make sure a session array exists for this form and create one if it doesn't
	 */
	protected function initFormSession() {
		if(!isset($_SESSION['forms'])) $_SESSION['forms'] = [];
		if(!isset($_SESSION['forms'][$this->id])) $_SESSION['forms'][$this->id] = [];
	}

	/**
	 * Gets the values from the session and populates the form fields with them. If you want to populate the form with the latest session values when the page first loads, you must call this after setting up your fields and before calling output(). Otherwise, it will only be called when you call the validate() function.
	 *
	 * @todo: Should we follow this same pattern for pulling post/get variables?
	 */
	public function pullSessionValues() {
		if(!$this->sessionStorage) return false;
		$this->initFormSession();

		foreach($this->fields() as $field) {
			if(isset($_SESSION['forms'][$this->id][$field->name])) {
				$field->value = $_SESSION['forms'][$this->id][$field->name];
			}
		}

		$this->sessionValuesPulled = true;
	}

	/**
	 * Call to remove all values for this form from the session. For example, after saving values to the database.
	 */
	public function clearSessionValues() {
		$this->initFormSession();

		unset($_SESSION['forms'][$this->id]);
	}

	protected function run() {
		$this->view->fieldsOut = $this->outputFields();
	}

	/**
	 * Gets values from post or get, sets the value for each field's $value attribute, and calls validate() on each field. Sets a global error message to $this->error and to $session->error. These messages may be overridden by the client code if desired.
	 *
	 * @param bool $pullValuesFromInput pulls input values from post or get before validating the form. Turn this off if you are manually setting your values in your your field setup and do not want this to overwrite them (default: true)
	 * @return bool true if all fields are valid, false if any are invalid.
	 *
	 * @todo: Delegate pulling input to an optional pullInput() or getInput() method on each fieldUI? One problem is that the field doesn't know the form method, but that could be changed by adding a method property to FieldUI, which might be helpful in the future when we do more ajax processing on individual fields(?)
	 */
	public function validate($pullValuesFromInput = true) {
		$valid = true;

		// If using session storage and pullSessionValues() has not been called yet, do so now
		if($this->sessionValuesPulled == false) $this->pullSessionValues();

		if($this->method == 'get' || $this->disableCSRF || $this->session->CSRF->hasValidToken()) {
			foreach($this->formFields as $field) {
				if(!$field instanceof FieldUI) continue;

				// Pull values from input/post
				if($pullValuesFromInput) {

					// Set the field's value attribute to the value from from post or get
					//$rawInputValue = $this->input->{$this->method}->{$field->name}; // $input doesn't support multidimensional arrays :(
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

						if($this->sessionStorage) {
							$_SESSION['forms'][$this->id][$field->name] = $rawInputValue;
						}
					}
				}

				if(!$field->validate()) $valid = false; // Run the field's validate function
			}

			if(!$valid) {
				$this->error = "Whoops! Something's not right. Please correct the errors indicated below and resubmit the form.";
			}
			else { // Successful
				if($this->method == 'post') $this->session->success = "Saved.";
			}
		}
		else {
			$valid = false;
			$this->error = "We've detected a possible security issue (CSRF) with your submission. Just to be safe, please reload the page and try again.";
		}

		if($this->error) $this->session->error = $this->error;

		return $valid;
	}

	/**
	 * @param FieldUI $field Adds a field to the form
	 */
	public function addField(UI $field) {
		$this->formFields[] = $field;
	}

	/**
     * @param FieldUI $field Removes a field from the form
     */
    public function removeField(FieldUI $field) {
        for($i = count($this->formFields)-1; $i >= 0; $i--) {
            $f = $this->formFields[$i];
            if($f == $field) {
                array_splice($this->formFields,$i,1);
            }
        }
    }

	/**
	 * @param $name - Name of field to return
	 * @return Returns all fields or a single field matching the given name
	 */
	public function fields($name = null) {
		if($name) {
			foreach($this->formFields as $field) {
				if($field->name == $name) return $field;
			}
		}
		else return $this->formFields;
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

}