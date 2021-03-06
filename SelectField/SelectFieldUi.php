<?php namespace ProcessWire;
class SelectFieldUi extends FieldUi {

	public $__options = []; // Each option expects a label and a value
	public $cssClass = 'selectField';
	public $autocomplete = false; // Progressively enhances the search field with autocomplete search
	
	protected function setup() {
		if($this->autocomplete) {
			$this->headScripts[] = $this->url . 'chosen/chosen.jquery.min.js';
			$this->styles[] = $this->url . 'chosen/chosen.min.css';
		}
	}

	public function fieldValidate() {
		$optionMatch = false;
		foreach($this->options as $option) {
			if($option['value'] == $this->value) {
				$optionMatch = true;
				break;
			}
		}
		if(!$optionMatch) $this->error = __('Invalid option.');

		if($this->error) return false;
		else return true;
	}

	public function run() {
		$selectedLabel = '';
		foreach($this->options as $option) {
			if($option['value'] == $this->value) {
				$selectedLabel = $option['label'];
				break;
			}
		}
		$this->view->selectedLabel = $selectedLabel;
		
		return parent::run();
	}
}