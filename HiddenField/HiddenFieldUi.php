<?php namespace ProcessWire;
class HiddenFieldUi extends FieldUi {
	
	public function filter() {
		// Filter text w/ ProcessWire's sanitizer
		$this->value = $this->sanitizer->text($this->value, ['maxLength' => 0]);
	}

	public function fieldValidate() {
		if((int)$this->maxLength > 0) {
			if(strlen($this->value) > (int)$this->maxLength) {
				$this->error = __('Max length exceeded.');
			}
		}

		if($this->error) return false;
		else return true;
	}
}