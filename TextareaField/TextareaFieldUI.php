<?php namespace ProcessWire;
class TextareaFieldUI extends FieldUI {
	public $rows = 5;
	public $maxLength = 16384;
	public $cssClass = 'textareaField';

	public function filter() {
		$this->value = $this->sanitizer->textarea($this->value, array('maxLength' => 0));
	}
	
	public function fieldValidate() {

		if((int)$this->minLength > 0) {
			if(strlen($this->value) < (int)$this->minLength) {
				$this->error = 'Minimum length has not been met.';
			}
		}

		if((int)$this->maxLength > 0) {
			if(strlen($this->value) > (int)$this->maxLength) {
				$this->error = 'Max length exceeded.';
			}
		}

		if($this->error) return false;
		else return true;
	}
}