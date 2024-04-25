<?php namespace ProcessWire;
/*
 * NOTE: This field should be provided a value in the same format that it will display to the user (the same format specified in the $jsDateFormat and $phpDateFormat properties below). When saving, you must manually convert the value back to your database format.
 */
class DateFieldUi extends FieldUi {

	public $cssClass = 'dateField';
	public $jsDateFormat = 'YYYY/MM/DD'; // Note: YYYY-MM-DD Doesn't seem to work with the current version of Pikaday
	public $phpDateFormat = 'Y/m/d'; // The php date format must be equivalent to the js format above. These are different because JS and PHP use different character codes to represent the same formats.
	public $maxLength;
//	public $minDate;
	public $__minDate;
    public $maxDate;

	public function setup() {
		$this->headScripts[] = $this->config->urls->templates . "library/moment.min.js"; // Including moment.js enables additional date format options in pikaday
		$this->styles[] = $this->url . "pikaday/pikaday.css";
		$this->headScripts[] = $this->url . "pikaday/pikaday.js";
	}

	protected function fieldValidate() {
		if((int)$this->maxLength > 0) {
			if(strlen($this->value) > (int)$this->maxLength) {
				$this->error = __('Max length exceeded.');
			}
		}

		if(\DateTime::createFromFormat($this->phpDateFormat, $this->value) === false) {
			$this->error = __('Incorrect date format.');
		}

		if($this->error) return false;
		else return true;
	}
}