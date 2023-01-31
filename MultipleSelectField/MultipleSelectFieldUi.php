<?php namespace ProcessWire;
class MultipleSelectFieldUi extends FieldUi {
	public $value = [];
	public $__options = []; // Each option expects a label and a value
	public $cssClass = 'multipleSelectField';
    public $minimumCountSelected;

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

    public function run() {
        $selectedLabel[] = null;
        foreach($this->options as $option) {
            if(in_array($option['value'], $this->value)) {
                $selectedLabel[] = $option['label'];
                //break;
            }
        }
        $this->view->selectedLabel = substr(implode(', ', $selectedLabel), 2); // Always adds ', ' in front of string even when checking if label and/or value are null

        return parent::run();
    }

}