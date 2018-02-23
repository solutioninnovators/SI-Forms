<?php namespace ProcessWire;
class TextFieldUI extends FieldUI {
	public $type = 'text'; // Options include text, email, password
	public $attributes = array(); // Any additional html attributes
	public $maxLength = 100;
	
	public function validate() {

		$this->value = trim($this->value);

		if($this->value == '') {
			if($this->required == false) return true;
			else $this->error = 'Required.';
		}

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

		// Filter text w/ ProcessWire's sanitizer
		$this->value = $this->sanitizer->text($this->value, array('stripTags' => false, 'maxLength' => $this->maxLength));

		if($this->type == 'email') {
			if(!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
				$this->error = 'Invalid email address.';
			}
		}

		if($this->error) return false;
		else return true;
	}

	protected function run() {
		// Convert attributes array into a string for output
		$attributeString = '';
		foreach($this->attributes as $attrKey => $attrValue) {
			$attributeString .= "$attrKey=\"$attrValue\" ";
		}
		$this->view->attributeString = $attributeString;
	}
}