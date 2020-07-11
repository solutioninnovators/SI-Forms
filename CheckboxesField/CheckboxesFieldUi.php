<?php namespace ProcessWire;
/**
 * Class CheckboxesFieldUi
 * @package ProcessWire
 * 
 * Supports one or more checkboxes, defined by the options array.
 * 
 */
class CheckboxesFieldUi extends FieldUi {

	public $value = [];
	public $__options = []; // Each option expects a label and a value
	public $columnize = false; // Sort checkboxes into columns when space allows
	public $bulkSelectOptions = false;
	public $cssClass = 'checkboxesField';

	protected function setup() {}

	protected function fieldValidate() {
		foreach($this->value as $val) {
			// Check each value to see if it is a valid option in the options array
			$match = false;
			foreach($this->options as $option) {
				if($option['value'] == $val) {
					$match = true;
					break;
				}
			}
			if(!$match) {
				$this->error = __('One or more of the values submitted are not allowed for this field.');
				break;
			}

		}

		
		if($this->error) return false;
		else return true;
	}
}