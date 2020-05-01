<?php namespace ProcessWire;
class SelectFieldUI extends FieldUI {

	public $options = array(); // Each option expects a label and a value
	public $autocomplete = false; // Progressively enhances the search field with autocomplete search
	
	protected function setup() {
		if($this->autocomplete) {
			$this->headScripts[] = $this->config->urls->templates . 'library/chosen/chosen.jquery.min.js';
			$this->styles[] = $this->config->urls->templates . 'library/chosen/chosen.min.css';
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
		if(!$optionMatch) $this->error = 'Option does not exist.';

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
	}
}