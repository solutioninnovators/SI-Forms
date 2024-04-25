<?php namespace ProcessWire;
class TextFieldUi extends FieldUi {
	public $type = 'text'; // Options include text, email, password
	public $attributes = []; // Any additional html attributes to add to the input
	public $maxLength = 255;
	public $cssClass = 'textField';
	public $honeypot = false; // Is this a honeypot field?

	public function filter() {
		$this->value = $this->sanitizer->text($this->value, array('maxLength' => 0, 'trim' => false));
	}

	public function fieldValidate() {
		if($this->honeypot && $this->value) $this->session->redirect('./');

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

		if($this->type === 'email') {
			if(!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
				$this->error = __('Incomplete email address.');
			}
		}

		if($this->error) return false;
		else return true;
	}

	protected function run() {
		// Convert attributes array into a string for output
		$attributeString = '';
		foreach($this->attributes as $attrKey => $attrValue) {
			$attributeString .= $this->sanitizer->entities1($attrKey) . '="' . $this->sanitizer->entities1($attrValue) . '" ';
		}
		$this->view->attributeString = $attributeString;

		return parent::run();
	}
}