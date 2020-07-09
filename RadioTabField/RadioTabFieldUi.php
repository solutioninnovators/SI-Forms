<?php namespace ProcessWire;
class RadioTabFieldUi extends FieldUi {
	public $options = array(); // Each option expects a label and a value
	public $value = '';

	public function run() {
		$this->cssClass = "radioTab field_{$this->name} tabs";
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