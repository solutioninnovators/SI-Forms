<?php namespace ProcessWire;
class TimeFieldUI extends FieldUI {

	public $twentyFourHour = true; // Limits the field to a 24 hour period
	public $amPm = false; // Uses 12 hour format with am & pm (requires $twentyFourHour == true)
	public $hideIncrementButtons = false;
	
	protected function setup() {
		$this->headScripts[] = "{$this->urls->templates}library/si-time.js";
	}

	protected function run() {
		if($this->twentyFourHour == true) {
			if($this->amPm) $this->maxLength = 7;
			else $this->maxLength = 5;
		}
	}

	public function fieldValidate() {

		if((int)$this->maxLength > 0) {
			if(strlen($this->value) > (int)$this->maxLength) {
				$this->error = 'Max length exceeded.';
			}
		}

		if($this->twentyFourHour) {
			$regex = '/^\d{1,2}:\d{2}$/';
			if($this->amPm) {
				$regex = '/^\d{1,2}:\d{2}[AaPp][Mm]$/';
			}
		}

		else $regex = '/^\d+:\d{2}$/';

		if(!preg_match($regex, $this->value)) {
            $this->error = 'Invalid time format.';
        }

		// Filter text w/ ProcessWire's sanitizer
		$this->value = $this->sanitizer->text($this->value, array('maxLength', $this->maxLength));

		if($this->error) return false;
		else return true;
	}
}