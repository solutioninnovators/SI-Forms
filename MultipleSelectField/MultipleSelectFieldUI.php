<?php namespace ProcessWire;
class MultipleSelectFieldUI extends FieldUI {
	public $options = []; // Each option expects a label and a value
	public $value = [];
	public $extraAttributes = [];

	protected function setup() {
		$this->styles[] = $this->config->urls->templates . 'library/multiple-select-master/multiple-select.css';
		$this->headScripts[] = $this->config->urls->templates . 'library/multiple-select-master/multiple-select.js';
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