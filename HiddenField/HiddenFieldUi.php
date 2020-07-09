<?php namespace ProcessWire;
class HiddenFieldUi extends FieldUi {
	
	public function fieldValidate() {
		if((int)$this->maxLength > 0) {
			if(strlen($this->value) > (int)$this->maxLength) {
				$this->error = __('Max length exceeded.');
			}
		}

		// Filter text w/ ProcessWire's sanitizer @todo: move to proper filter function
		$this->value = $this->sanitizer->text($this->value, array('maxLength' => $this->maxLength));

		if($this->error) return false;
		else return true;
	}
}