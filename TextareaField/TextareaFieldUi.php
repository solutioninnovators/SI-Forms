<?php namespace ProcessWire;
class TextareaFieldUi extends FieldUi {
	public $rows = 5;
	public $maxLength = 16384;
	public $cssClass = 'textareaField';

	public function filter() {
		$this->value = $this->sanitizer->textarea($this->value, array('maxLength' => 0));
	}
	
	public function fieldValidate() {

		if((int)$this->minLength > 0) {
			if(strlen($this->value) < (int)$this->minLength) {
				$this->error = __('Minimum length has not been met.');
			}
		}

		if((int)$this->maxLength > 0) {
			if(strlen($this->value) > (int)$this->maxLength) {
				$this->error = __('Max length exceeded.');
			}
		}

		if($this->error) return false;
		else return true;
	}
}