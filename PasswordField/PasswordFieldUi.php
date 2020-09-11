<?php namespace ProcessWire;
class PasswordFieldUi extends FieldUi {

    public $cssClass = 'passwordField';
    public $confirmPassword = false;
    public $visibilityToggle = true;
    public $testStrength = true;
    public $maxLength = 300;
    public $labelConfirm = '';
    public $placeholderConfirm = '';
    public $stackInputs = false; // Stack the confirmation input under the first input instead of beside it
    public $showValue = false;
    public $value = ['',''];

    public function fieldValidate() {

        if($this->testStrength && !$this->checkPassword($this->value[0],$errors)) {
            $this->error = implode('<br>',$errors);
        }
        elseif($this->confirmPassword) {  // If need to confirm the password
            if($this->required && $this->value[1] === '') {
                $this->error = __('Please confirm your password.');
            }
            elseif($this->value[0] !== $this->value[1]) {
                $this->error = __('The passwords entered do not match.');
            }
        }

		if($this->error) return false;
		else return true;
	}


    public function checkPassword($pwd, &$errors) {
        $errors_init = $errors;

        if(strlen($pwd) < 8) {
            $errors[] = __("Password must be at least 8 characters long.");
        }

        if(!preg_match("#[0-9]+#", $pwd)) {
            $errors[] = __("Password must include at least one number.");
        }

        if(!preg_match("#[a-zA-Z]+#", $pwd)) {
            $errors[] = __("Password must include at least one letter.");
        }

        return ($errors == $errors_init);
    }
}