<?php namespace ProcessWire;
class ButtonFieldUI extends FieldUI {
	public $href = ''; // Specifying an href will cause the button to be created as an <a> link rather than html <button>
	public $type = 'submit'; // e.g. button, reset, or submit (use 'button' for js-only buttons)
	public $attributes = array(); // Any additional html attributes to include on the button
	public $btnClasses = '';
	public $fullWidth = true;
	public $version = 2;

	public function validate() {
		return true;
	}

	public function run() {
		// Convert attributes array into a string for output
		$attributeString = '';
		foreach($this->attributes as $attrKey => $attrValue) {
			$attributeString .= "$attrKey=\"$attrValue\" ";
		}
		$this->view->attributeString = $attributeString;
	}
}