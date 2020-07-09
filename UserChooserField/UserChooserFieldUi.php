<?php namespace ProcessWire;
/**
 * Class UserChooserFieldUI
 * @package ProcessWire
 * 
 * Progressively enhances a list of checkboxes with user pictures
 *
 * @todo: Use <label> tag to make field work without needing js?
 * 
 */
class UserChooserFieldUi extends FieldUi {

	public $options; // PageArray
	public $value = array();
	public $singular = false; // Only allows selection of a single user

	protected function setup() {}

	protected function run() {

	}

	public function fieldValidate() {
		if($this->required && !count($this->value)) {
			$this->error = __('You must select a user.');
		}
		elseif(count($this->value)) {
			foreach($this->value as $val) {
				$id = (int)$val;
				$match = $this->options->findOne($id);

				if(!$match->id) {
					$this->error = __('One or more of the values submitted are not allowed for this field.');
					break;
				}
			}
		}
		
		if($this->error) return false;
		else return true;
	}
}