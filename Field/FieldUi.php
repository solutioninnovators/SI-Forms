<?php namespace ProcessWire;
#[\AllowDynamicProperties]

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
	public $saveCallback; // Set a callback to run in place of the default save procedure. Should return the value to be saved. If necessary you may also save the field within the callback and return null, though this is less efficient than allowing the Form to manage the save.
	
	public $__classes;
	public $__placeholder = '';
	public $__disabled = false;
	public $ajaxValidate = false; // Validate the field with ajax (but don't save)
	public $ajaxSave = false; // Turn on ajax save (also validates)
	public $savePage; // Optional ProcessWire Page object to save the field to (used for AJAX saves). Set to false if you wish to disable automatic saving for this field even if you have savePage set on the form.
	public $saveField; // Optional ProcessWire Field name - if different from this UI's name property (used for AJAX saves)
	public $__icon = '';
	public $__label = '';
    public $__labelTooltip = '';
	public $__showLabel = true;
	public $__required = false;
	public $requiredErrorMsg = 'Required';
	public $__description = ''; // Text that appears before field
	public $__notes = ''; // Text that appears below field
	public $__inputClasses = '';
	public $__readOnly = false; // Set to true to display the value of the field without allowing user modification. (The field may still post its data back to the form, so it is up to the developer to disregard it during save if they want to be absolutely sure the data hasn't been manipulated by a savvy user).
	public $__autofocus = false; // Set focus to this field when the page loads
	public $__autocomplete = false; // Turn on/off browser autocomplete functionality
	public $__show = true; // Should the field be shown or hidden? (for field dependencies)
	public $__index; // Optional integer index to change the order that the fields are output in. This allows fields to be processed in a different order than they are displayed in (if field dependency logic requires fields to be defined in an order that is different from their display order)
	public $extraAttributes = []; // An associative array of additional attributes to add to the field div wrapper @todo: Change to fieldAttributes
	public $cssClass = ''; // String of classes to add to the field wrapper @todo: Change to fieldClass?
	public $__dependsOn = []; // Array containing the names of fields that this field depends on. If the value of any of these fields changes, this field will reload via ajax
	public $resetValueOnReload = false; // If set to true, any time this field reloads as a result of a change of the value of another field specified in its $dependsOn array, the value of this field will reset to the default value, even if the user populated it with something else.
	
	public $nonNull = false; // A field that is nonNull is always considered populated (e.g. a checkbox)

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
	 * This should NOT be overridden by subclasses. Instead, override the fieldValidate() method.
	 *
	 * @return boolean
	 */
	public function validate() {

		// If there is any filtering/sanitization (optional), run it before validation (security best practice)
		$this->filter();
		if(FormUi::isCallback($this->filterCallback)) {
			$return = call_user_func_array($this->filterCallback, [$this, $this->form]);
			if($return) {
				$this->value = $return;
			}
		}

        // If validateOverride then skip normal validation
        if(!$this->validateOverride) {
			// If field is not populated, check if it's required
			if(!$this->isPopulated()) {
				if(!$this->required) {
					// If it's not populated and not required, we can skip further validation
					return true;
				}
				else {
					if(!$this->error) $this->error = $this->requiredErrorMsg;
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
	 * Uses the form's savePage if the field doesn't have one.
	 *
	 * NOTE: Does NOT validate before saving! Call validate() first!
	 *
	 * @param bool forceSaveNow - Specify true to override the default behavior and force the field to save changes immediately no matter what
	 */
	public function save($forceSaveNow = false) {
		if($this->readOnly) return; // Skip read only fields

		$savePage = null;
		$saveNow = true;

		if($this->savePage) {
			$savePage = $this->savePage;
		}
		elseif(isset($this->form) && isset($this->form->savePage)) { // Fallback to the form's savePage setting
			$savePage = $this->form->savePage;
			$saveNow = false; // If using the form's savePage setting, we wait until all fields have been set to save off the page
		}

		// If no saveField specified, fallback to the field's UI name
		if($this->saveField) {
			$saveField = $this->saveField;
		} else {
			$saveField = $this->name;
		}

		if(FormUi::isCallback($this->saveCallback)) {
			$result = call_user_func_array($this->saveCallback, [$this, $this->form, $savePage, $saveField]);
		}
		else {
			$result = $this->fieldSave();
		}

		// If a value was returned, we'll save it to the field. Otherwise, we assume that the function already saved the field.
		if(!is_null($result)) {
			$of = $savePage->of(false);
			$savePage->set($saveField, $result);
			if($saveNow || $forceSaveNow)
				$return = $savePage->save($saveField); // If the savePage is set on the field rather than the form, save it right away
			$savePage->of($of);
		}
		else {
			$return = true;
		}

		return $return;
	}


	/**
	 * Default save procedure. Subclasses should overwrite this method, if necessary.
	 * Note that $this->savePage and $this->saveField are prepared at this point
	 *
	 * @return mixed - Return the value that should be saved to the saveField on the savePage
	 */
	protected function fieldSave() {
		return $this->value;
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

		$this->view->formAttribute = $this->form && !$this->form->legacyMode && $this->form->id ? 'form="'.$this->form->id.'"' : '';
	}

	public function siblings($name = null) {
		if($this->parent && $this->parent instanceof FieldUi) {
			if($name) {
				$searchName = $this->parent->name;
				if(isset($this->iteration)) $searchName .= "[$this->iteration]";
				$searchName .= "[$name]";

				return $this->form->findField($this->parent->children, $searchName);
			}
			else {
				return $this->parent->children;
			}
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