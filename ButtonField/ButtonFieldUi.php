<?php namespace ProcessWire;

class ButtonFieldUi extends FieldUi {
	public $__href = ''; // Specifying an href will cause the button to be created as an <a> link rather than html <button>
	public $type = 'submit'; // e.g. button, reset, or submit (use 'button' for js-only buttons)
	public $attributes = []; // Any additional html attributes to include on the button
	public $btnClasses = '';
	public $fullWidth = false;

	public function run() {
		// Convert attributes array into a string for output
		$attributeString = '';
		foreach($this->attributes as $attrKey => $attrValue) {
			$attributeString .= $this->sanitizer->entities1($attrKey) . '="' . $this->sanitizer->entities1($attrValue) . '" ';
		}
		$this->view->attributeString = $attributeString;
		
		return parent::run();
	}
}