<?php namespace ProcessWire;

class NumberFieldUi extends FieldUi {

	public $cssClass = 'numberField';
	public $max;
	public $min;

	public function fieldValidate() {

		if((int)$this->max > 0) {
			if(strlen($this->value) > (int)$this->max) {
				$this->error = __('Maximum value is ') . $this->max;
			}
		}

        if((int)$this->min > 0) {
            if((int)($this->value) < (int)$this->min) {
                $this->error = __('Minimum value is ') . $this->min;
            }
        }

		//$regex = '/^[0-9]*$/';
        $regex = '/^[-+]?\d+(\.\d+)?$/';

		if(!preg_match($regex, $this->value)) {
            $this->error = __('Invalid number.');
        }


		if($this->error) return false;
		else return true;
	}
}