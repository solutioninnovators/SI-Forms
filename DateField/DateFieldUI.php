<?php namespace ProcessWire;
class DateFieldUI extends FieldUI {

	public function setup() {
		$this->headScripts[] = $this->config->urls->templates . "library/moment.min.js";
		$this->styles[] = $this->config->urls->templates . "library/pikaday/pikaday.min.css";
		$this->headScripts[] = $this->config->urls->templates . "library/pikaday/pikaday.min.js";
	}

	protected function fieldValidate() {
		if((int)$this->maxLength > 0) {
			if(strlen($this->value) > (int)$this->maxLength) {
				$this->error = 'Max length exceeded.';
			}
		}

		if(\DateTime::createFromFormat($this->user->dateFormatString(), $this->value) === false) {
			$this->error = 'Date must be in the MM/DD/YYYY format.';
		}

		if($this->error) return false;
		else return true;
	}
}