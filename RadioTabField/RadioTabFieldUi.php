<?php namespace ProcessWire;
class RadioTabFieldUi extends FieldUi {
	public $__options = []; // Each option expects a label and a value. NOTE: labels allow HTML content, so any user input must be escaped
	public $value = '';

	public function run() {
		$this->cssClass = "radioTab field_{$this->name} tabs";
		
		return parent::run();
	}

	public function fieldValidate() {
		$match = false;
		foreach($this->options as $option) {
			if($option['value'] == $this->value) {
				$match = true;
				break;
			}
		}

		if(!$match) {
			$this->error = __("Invalid option.");
			return false;
		}
		else {
			return true;
		}
	}
}