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
class UserChooserFieldUI extends FieldUI {

	public $options; // PageArray
	public $value = array();
	public $singular = false; // Only allows selection of a single user

	protected function setup() {}

	protected function run() {

	}

	public function validate() {
		if($this->required && !count($this->value)) {
			$this->error = 'You must select a user.';
		}
		elseif(count($this->value)) {
			foreach($this->value as $val) {
				$id = (int)$val;
				$match = $this->options->findOne($id);

				if(!$match->id) {
					$this->error = 'One or more of the values submitted are not allowed for this field.';
					break;
				}
			}
		}
		
		if($this->error) return false;
		else return true;
	}
}