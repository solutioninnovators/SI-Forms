<?php namespace ProcessWire;
/**
 * Class FieldUi
 * @package ProcessWire
 *
 * The FieldUI class is the base for inputfields. Fields are responsible for handling and validating their own input, as well as outputting their own html markup.
 *
 */
abstract class FieldUi extends Ui {

	public $name = ''; // Name of the html input. If this field maps to a field on a ProcessWire Page and you're using ajaxSave, the name should be the same

	public $form; // Holds a reference to the form that this field is a part of (if it is part of a form)

	public $value = ''; // Holds the value of the field
	public $error = ''; // Holds an error if the field didn't validate
	public $filterCallback; // Set a callback function to get called before validation to filter/sanitize the value
	public $validateCallback; // Set a callback function to get called after the default validation
	public $validateOverride = false; // Skip the field's standard validation logic and only run the validateCallback
	public $saveCallback; // Set a callback to run in place of the default save procedure. Must return true or false.
	
	public $classes;
	public $message = ''; // Hold a message
	public $placeholder = '';
	public $disabled = false;
	public $ajaxValidate = false; // Validate the field with ajax (but don't save)
	public $ajaxSave = false; // Turn on ajax save (also validates)
	public $savePage; // Optional ProcessWire Page object or ID to save the field to (used for AJAX saves)
	public $saveField; // Optional ProcessWire Field name - if different from this UI's name property (used for AJAX saves)
	public $icon = '';
	public $label = '';
	public $showLabel = true;
	public $required = false;
	public $description = ''; // Text that appears before field
	public $notes = ''; // Text that appears below field
	public $inputClasses = '';
	public $readOnly = false; // Set to true to display the value of the field while preventing modification or posting to the form
	public $autofocus = false; // Set focus to this field when the page loads
	public $autocomplete = false; // Turn on/off browser autocomplete functionality
	public $show = true; // Should the field be shown or hidden? (for field dependencies)
	public $index; // Optional integer index to change the order that the fields are output in. This allows fields to be processed in a different order than they are displayed in (if field dependency logic requires fields to be defined in an order that is different from their display order)
	public $extraAttributes = []; // An associative array of additional attributes to add to the field div wrapper
	public $cssClass = ''; // String of classes to add to the field wrapper

	protected $optionCallbacksRun = false;


	protected $callbackableOptions = [
		'options',
		'required',
		'disabled',
		'readOnly',
		'classes',
		'label',
		'notes',
		'description',
		'show'
	];


	/**
	 * Checks for any options that were specified as callbacks, runs them, and sets the value of the option to the result returned by the function
	 */
	public function runOptionCallbacks() {
		if($this->optionCallbacksRun === true) return;

		foreach($this->callbackableOptions as $callbackableOption) {
			if(FormUi::isCallback($this->$callbackableOption)) {
				$this->$callbackableOption = call_user_func_array($this->$callbackableOption, [$this, $this->form]);
			}
		}

		$this->optionCallbacksRun = true;
	}

	/**
	 * The validate method evaluates $this->value and returns true or false depending on whether it is a valid input for the given field. If an error is discovered, it will populate $this->error
	 *
	 * @return boolean
	 */
	public function validate() {

		// If there is any filtering/sanitization (optional), run it before validation (security best practice)
		$this->filter();
		if(FormUi::isCallback($this->filterCallback)) {
			call_user_func_array($this->filterCallback, [$this, $this->form]);
		}

		// If field is not populated, check if it's required
		if(!$this->isPopulated()) {
			if(!$this->required) {
				return true;
			}
			else {
				$this->error = __('Required');
				return false;
			}
		}

        // If validateOverride then skip normal validation
        if(!$this->validateOverride) {
            // Check the subclass's default validation requirements
            if(!$this->fieldValidate()) {
                return false;
            }
        }

		// If there is a callback provided, run it
		if(FormUi::isCallback($this->validateCallback)) {
			$result = call_user_func_array($this->validateCallback, [$this, $this->form]);
			if(is_string($result)) {
				$this->error = $result; // If a string is returned, use it as the error
				return false;
			}
			elseif(!$result) {
				return false;
			}
		}

		// If there is an error but we forgot to return false at any point above, do so now
		if($this->error) {
			return false;
		}

		return true;
	}

	protected function filter() {
		// Subclass may implement this
	}

	protected function fieldValidate() {
		// The subclass should overwrite this
		return true;
	}

	/**
	 * Is this field currently populated? This method may be overridden in subclasses that require custom logic
	 *
	 * @return bool
	 */
	protected function isPopulated() {
		$isPopulated = true;

		if(is_array($this->value)) {
			$filteredArray = array_filter($this->value, function($value) { return $value !== '' && $value !== null; });

			if(count($filteredArray) === 0) {
				$isPopulated = false;
			}
		}
		else {
			if($this->value === '' || $this->value === null) {
				$isPopulated = false;
			}
		}

		return $isPopulated;
	}

	/**
	 * Save the field based on the savePage and saveField values defined
	 * NOTE: Does NOT validate before saving! Call validate() first!
	 */
	public function save() {
		// If $savePage specified as ID, convert to Page
		if($this->savePage instanceof Page) {
			$savePage = $this->savePage;
		} else {
			$savePage = $this->pages->get((int)$this->savePage);
		}

		if(!$savePage->id) return false;

		// If no saveField specified, fallback to UI name
		if($this->saveField) {
			$saveField = $this->saveField;
		} else {
			$saveField = $this->name;
		}

		$this->saveField = $saveField;
		$this->savePage = $savePage;

		if(FormUi::isCallback($this->saveCallback)) {
			return call_user_func_array($this->saveCallback, [$this, $this->form]);
		}
		else {
			return $this->fieldSave();
		}
	}

	/**
	 * Default save procedure. Subclasses should overwrite this method, if necessary.
	 * Note that $this->savePage and $this->saveField are prepared at this point
	 * @return bool
	 */
	protected function fieldSave() {
		return $this->savePage->setAndSave($this->saveField, $this->value);
	}

	/**
	 * Placeholder method for optional implementation in subclasses.
	 * Called immediately after this field is populated with either the session, post/get, or initial/database values.
	 */
	public function afterValueSet() {}


	/**
	 * Get a property. If its value is a callback, process the callback first.
	 *
	 * @param $property
	 */
	/*public function get($property) {
		if(FormUi::isCallback($this->$property)) {
			$this->$property = call_user_func_array($this->saveCallback, [$this, $this->form]);
		}
		return $this->$property;
	}*/

}