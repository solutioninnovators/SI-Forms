<?php namespace ProcessWire;
class DateFieldUi extends FieldUi {

	public $cssClass = 'dateField';
	public $jsDateFormat = 'YYYY-MM-DD';
	public $phpDateFormat = 'Y-m-d';
	public $maxLength;

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