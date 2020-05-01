<?php namespace ProcessWire;
class TextFieldUI extends FieldUI {
	public $type = 'text'; // Options include text, email, password
	public $attributes = array(); // Any additional html attributes
	public $maxLength = 255;
	public $cssClass = 'textField';
	public $honeypot = false; // Is this a honeypot field?

	public function filter() {
		$this->value = $this->sanitizer->text($this->value, array('maxLength' => 0));
	}

	public function fieldValidate() {
		if($this->honeypot && $this->value) $this->session->redirect('./');

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

		if($this->type === 'email') {
			if(!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
				$this->error = 'Incomplete email address.';
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