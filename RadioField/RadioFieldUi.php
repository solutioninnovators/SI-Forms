<?php namespace ProcessWire;
class RadioFieldUi extends FieldUi {
	public $__options = []; // Each option expects a label and a value
	public $value = '';
    public $cssClass = 'radioField';

	protected function setup() {}

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