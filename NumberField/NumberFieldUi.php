<?php namespace ProcessWire;

class NumberFieldUi extends FieldUi {

	public $cssClass = 'numberField';
	public $max;
	public $min;
	public $step;
	public $maxLength;
    public $removeButtons;

	public function fieldValidate() {

		if((int)$this->max > 0) {
			if((int) $this->value > (int)$this->max) {
				$this->error = __('Maximum value is ') . $this->max;
			}
		}

        if((int)$this->min > 0) {
            if((int)($this->value) < (int)$this->min) {
                $this->error = __('Minimum value is ') . $this->min;
            }
        }

        $regex = '/^[-+]?\d+(\.\d+)?$/';

		if(!preg_match($regex, $this->value)) {
            $this->error = __('Invalid number.');
        }

		// This is not reliable due to floating point issues
//		if($this->step && $this->value && (fmod((float)$this->value, (float)$this->step) != 0)) {
//			$this->error = sprintf(__('Value must be a multiple of %s.'), $this->step);
//		}

		if($this->error) return false;
		else return true;
	}
}