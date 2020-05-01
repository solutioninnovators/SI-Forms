<?php namespace ProcessWire;
class RadioFieldUI extends FieldUI {
	public $options = array(); // Each option expects a label and a value
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
			$this->error = "Option doesn't exist";
			return false;
		}
		else {
			return true;
		}
	}
}