<?php namespace ProcessWire;
class HiddenFieldUI extends FieldUI {
	
	public function validate() {
		if($this->value == '') {
			if($this->required == false) return true;
			else $this->error = 'Required.';
		}

		if((int)$this->maxLength > 0) {
			if(strlen($this->value) > (int)$this->maxLength) {
				$this->error = 'Max length exceeded.';
			}
		}

		// Filter text w/ ProcessWire's sanitizer
		$this->value = $this->sanitizer->text($this->value, array('maxLength' => $this->maxLength));

		if($this->error) return false;
		else return true;
	}
}