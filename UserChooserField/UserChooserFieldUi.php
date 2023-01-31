<?php namespace ProcessWire;
/**
 * Class UserChooserFieldUI
 * @package ProcessWire
 * 
 * Progressively enhances a list of checkboxes with user pictures
 *
 */
class UserChooserFieldUi extends FieldUi {

	public $__options; // PageArray
	public $cssClass = 'userChooserField';
	public $value = [];
	public $singular = false; // Only allows selection of a single user
	public $thumbnailSize = 75;
	public $showInitials = true;
	public $useDropDown = false;

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

	protected function fieldSave() {
		return $this->wire->pages->getById($this->value);
	}
}