<?php namespace ProcessWire;
class MultipleSelectFieldUi extends FieldUi {
	public $value = [];
	public $__options = []; // Each option expects a label and a value
	public $cssClass = 'multipleSelectField';

	protected function setup() {
		$this->styles[] = $this->url . 'multiple-select/multiple-select.css';
		$this->headScripts[] = $this->url . 'multiple-select/multiple-select.js';
	}

	public function fieldValidate() {
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