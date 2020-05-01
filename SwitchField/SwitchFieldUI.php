<?php namespace ProcessWire;
/**
 * Class CheckboxFieldUI
 * @package ProcessWire
 *
 * Supports one checkbox to represent a boolean true/false value. Acceptable values are 1 and 0. This field uses a hidden field to overcome a limitations of the html checkbox input and ensure that a value is always submitted for the checkbox even when it is unchecked.
 *
 */
class SwitchFieldUI extends FieldUI {

	public $value = null;

	protected function setup() {
		if($this->columnize) $this->headScripts[] = $this->config->urls->templates . 'library/jquery.columnizer.min.js';
	}

	protected function fieldValidate() {
		$valid = false;
		if($this->value == 1 || !$this->value) $valid = true;

		return $valid;
	}
}