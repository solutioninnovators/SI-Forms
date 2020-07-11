<?php namespace ProcessWire;
/**
 * Class FieldUi
 * @package ProcessWire
 *
 * The FieldUI class is the base for inputfields. Fields are responsible for handling and validating their own input, as well as outputting their own html markup.
 *
 * @todo: Instead of including a header and footer in the view, we could use a "field layout" block of sorts which could be overridden in the individual field's class or even passed in at run time.
 *
 */
abstract class FieldUi extends Ui {

	// Properties preceded by two underscores can be passed callback functions that will execute the first time their non-underscored counterpart is retrieved.

	public $name = ''; // Name of the html input. If this field maps to a field on a ProcessWire Page and you're using ajaxSave, the name should be the same

	public $form; // Holds a reference to the form that this field is a part of (if it is part of a form)

	public $value = ''; // Holds the value of the field
	public $error = ''; // Holds an error if the field didn't validate
	public $message = ''; // Hold a message

	public $filterCallback; // Set a callback function to get called before validation to filter/sanitize the value
	public $validateCallback; // Set a callback function to get called after the default validation
	public $validateOverride = false; // Skip the field's standard validation logic and only run the validateCallback
	public $saveCallback; // Set a callback to run in place of the default save procedure. Must return true or false.
	
	public $classes;
	public $__placeholder = '';
	public $__disabled = false;
	public $ajaxValidate = false; // Validate the field with ajax (but don't save)
	public $ajaxSave = false; // Turn on ajax save (also validates)
	public $savePage; // Optional ProcessWire Page object or ID to save the field to (used for AJAX saves)
	public $saveField; // Optional ProcessWire Field name - if different from this UI's name property (used for AJAX saves)
	public $__icon = '';
	public $__label = '';
	public $showLabel = true;
	public $__required = false;
	public $__description = ''; // Text that appears before field
	public $__notes = ''; // Text that appears below field
	public $__inputClasses = '';
	public $__readOnly = false; // Set to true to display the value of the field while preventing modification or posting to the form
	public $autofocus = false; // Set focus to this field when the page loads
	public $autocomplete = false; // Turn on/off browser autocomplete functionality
	public $__show = true; // Should the field be shown or hidden? (for field dependencies)
	public $index; // Optional integer index to change the order that the fields are output in. This allows fields to be processed in a different order than they are displayed in (if field dependency logic requires fields to be defined in an order that is different from their display order)
	public $extraAttributes = []; // An associative array of additional attributes to add to the field div wrapper
	public $cssClass = ''; // String of classes to add to the field wrapper

	//protected $_callbackQueue = []; // Holds callbacks until they are invoked for the first time

	/**
	 * When we try to set a property that does not exist, check if there is a corresponding placeholder property with the same name that is preceded by two underscores. If the value we're setting is a callback, store it in the placeholder for execution later. If it isn't, set it to a real property right away and unset the placeholder property.
	 *
	 * @param $name
	 * @param $value
	 */
	public function __set($name, $value) {
		$placeholderProperty = "__{$name}";

		if(property_exists($this, $placeholderProperty)) {
			// If the value we're setting is a callback function, we'll hold it in the callback queue for later processing
			if(FormUi::isCallback($value)) {
				$this->$placeholderProperty = $value;
			}
			else {
				$this->$name = $value;
				unset($this->$placeholderProperty);
			}
		}
		else {
			$this->$name = $value;
		}
	}

	/**
	 * When we try to get a property that doesn't exist, check if it has a placeholder counterpart starting with two underscores. If it has a callback function in it, run the callback and set a real property with the return value. If it's not a callback, copy the default from the placeholder to the real property. Unset the underscored placeholder property.
	 *
	 * @param string $name
	 * @return mixed|null
	 */
	public function __get($name) {
		$placeholderProperty = "__{$name}";

		if(property_exists($this, $placeholderProperty)) {
			if(FormUi::isCallback($this->$placeholderProperty)) {
				$result = call_user_func_array($this->$placeholderProperty, [$this, $this->form]);
			}
			else {
				$result = $this->$placeholderProperty;
			}
			unset($this->$placeholderProperty);
			$this->$name = $result;

			return $result;
		}
		else {
			return parent::__get($name);
		}
	}

	/**
	 * Loop through any remaining placeholder properties to check for unexecuted callbacks or copy over any default values so they can be passed to the view.
	 */
	private function runRemainingCallbacks() {
		foreach($this as $key => $value) {
			if(substr($key, 0, 2) === '__') {
				$realProperty = substr($key, 2); // Property name without the underscores

				// If the real property isn't already set, then run the callback or copy the default value from the placeholder so it will set the real property
				if(!property_exists($this, $realProperty)) {
					$this->__get($realProperty);

				}
				// If the real property exists at the same time as the placeholder, we will simply remove the placeholder. This would likely only ever happen if a field extending this class mistakenly redeclared a callable property without the underscore.
				else {
					unset($this->$key);
				}
			}
		}
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

        // If validateOverride then skip normal validation
        if(!$this->validateOverride) {
			// If field is not populated, check if it's required
			if(!$this->isPopulated()) {
				if(!$this->required) {
					return true;
				}
				else {
					if(!$this->error) $this->error = __('Required');
					return false;
				}
			}

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
		// The subclass should implement this
		return true;
	}

	/**
	 * Is this field currently populated? This method may be overridden in subclasses that require custom logic
	 *
	 * @return bool
	 */
	public function isPopulated() {
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
	 * All subclasses that implement their own run method must end by calling return parent::run()
	 */
	protected function run() {
		$this->runRemainingCallbacks();
		if(!$this->show) {
			$this->wrapperAttributes['style'] = 'display:none;';
		}
	}

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