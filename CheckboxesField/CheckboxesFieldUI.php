<?php namespace ProcessWire;
/**
 * Class CheckboxesFieldUI
 * @package ProcessWire
 * 
 * Supports one or more checkboxes, defined by the options array.
 * 
 */
class CheckboxesFieldUI extends FieldUI {

	public $options = []; // Each option expects a label and a value
	public $value = [];
	public $columnize = false; // Use the columnizer.js plugin to sort checkboxes into columns when space allows
	public $bulkSelectOptions = false;

	protected function setup() {
		if($this->columnize) $this->headScripts[] = $this->config->urls->templates . 'library/jquery.columnizer.min.js';
	}

	public function validate() {
		if(count($this->value)) {
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
					$this->error = 'One or more of the values submitted are not allowed for this field.';
					break;
				}

			}
		}
		else {
			if($this->required) $this->error = 'Required.';
		}
		
		if($this->error) return false;
		else return true;
	}
}