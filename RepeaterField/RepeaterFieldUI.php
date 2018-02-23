<?php namespace ProcessWire;

/**
 * Class RepeaterFieldUI
 * @package ProcessWire
 * 
 * Takes any other FieldUI and makes it repeatable. The values returned from the individual fields are stored as a multidimensional array
 */
class RepeaterFieldUI extends FieldUI {
	public $itemTemplate; // The field object of type FieldUI to repeat. This should not have a value. Name is optional and will only be used for multiple templates.
	public $value = [];
	public $showBlankItem = true; // Show a new item right away without having to click the "add" button

	private $children; // The items (rows) in the repeater
	private $initialValue; // Value set when object was first constructed
	
	protected function setup() {
		$this->initialValue = $this->value; // Save a copy of the initial value so we can compare it later
		$this->children = $this->constructRepeaterItems(); // Construct repeater items based on the field's initial value (prior to validate() call)
	}

	/**
	 * Constructs the appropriate number of fields (repeater items) and updates their values based on the current $this->value array. Uses $this->fieldTemplate as the template for each repeater item.
	 */
	private function constructRepeaterItems() {
		$repeaterItems = [];
		$repeaterItemIteration = 0;

		foreach($this->value as $repeaterItemValue) {
			if(empty($repeaterItemValue)) continue;

			if(is_array($this->itemTemplate) && is_array($repeaterItemValue)) { // Array of item templates
				$repeaterItemValue = array_values($repeaterItemValue); // Remove the keys so we can just access the values by index
				
				$subfieldIteration = 0;
				foreach($this->itemTemplate as $subfieldTemplate) {
					$subfield = clone $subfieldTemplate;
					$subfield->name = $this->name . "[$repeaterItemIteration][$subfield->name]";
					$subfield->id = $this->id . $repeaterItemIteration . $subfield->id;
					$subfield->value = $repeaterItemValue[$subfieldIteration];
					$repeaterItems[$repeaterItemIteration][] = $subfield;

					$subfieldIteration++;
				}
			}
			else { // Single item template
				$subfield = clone $this->itemTemplate;
				$subfield->name = $this->name . "[$repeaterItemIteration]";
				$subfield->id = $this->id . $repeaterItemIteration;
				$subfield->value = $repeaterItemValue;
				$repeaterItems[] = $subfield;
			}

			$repeaterItemIteration++;
		}
		return $repeaterItems;
	}

	/**
	 * @return bool
	 * The calling form will have already set $this->value to the new value coming from post or get, so we just have to check the current value of $this->value
	 */
	public function validate() {
		$valid = true;

		if($this->required && !count($this->value)) {
			$valid = false;
			$this->error = 'Required.';
		}
		else {
			// Reconstruct the fields array (repeater items) based on the new $this->value (if changed)
			if($this->value !== $this->initialValue) {
				$this->children = $this->constructRepeaterItems();
			}

			// Loop through each of the child fields and call its validate() function.
			foreach($this->children as $child) {
				if(is_array($child)) {
					foreach($child as $subfield) {
						if(!$subfield->validate()) $valid = false;
					}
				}
				else {
					if(!$child->validate()) $valid = false;
				}
			}

			// We can set an error message for the whole repeater field if any of its subfields are invalid, or just let each subfield show its own error
			//if(!$valid) $this->error = 'One or more of the values above is invalid.';
		}

		return $valid;
	}

	protected function run() {
		// Prepare output
		$repeaterItemsOut = [];

		// Create a hidden blank template item for javascript to use to make new items
		// NOTE: If the '$' element is appearing in the array, that means that one of the item template fields probably does not yet support the "disabled" property, so it is getting submitted. The field in question should be fixed.
		// @todo: DRY out this array itemTemplate cloning stuff
		if(is_array($this->itemTemplate)) {
			$repeaterRow = '';
			foreach($this->itemTemplate as $fieldTemplate) {
				$newField = clone $fieldTemplate;
				$newField->name = $this->name . "[$][$newField->name]";
				$newField->disabled = true;
				$repeaterRow .= $newField->output();
			}
			$repeaterItemsOut[] = $repeaterRow;
		}
		else {
			$template = clone $this->itemTemplate;
			$template->name = $this->name . '[$]';
			$repeaterItemsOut[] = $template->output();
		}

		// Render existing fields in $this->fields for the view
		foreach ($this->children as $field) {
			if(is_array($field)) {
				$repeaterRow = '';
				foreach($field as $subfield) {
					$repeaterRow .= $subfield->output();
				}
				$repeaterItemsOut[] = $repeaterRow;
			}
			else {
				$repeaterItemsOut[] = $field->output();
			}
		}

		// Create an additional blank field for new input
		if($this->showBlankItem || count($this->children) == 0) {
			$newItemIndex = count($this->children);

			if(is_array($this->itemTemplate)) {
				$repeaterRow = '';
				foreach($this->itemTemplate as $fieldTemplate) {
					$newField = clone $fieldTemplate;
					$newField->name = $this->name . "[$newItemIndex][$newField->name]";
					$newField->classes = $newField->classes .= ' repeaterField-newItem';
					$repeaterRow .= $newField->output();
				}
				$repeaterItemsOut[] = $repeaterRow;
			}
			else {
				$newField = clone $this->itemTemplate;
				$newField->name = $this->name . "[$newItemIndex]";
				$newField->classes = $newField->classes .= ' repeaterField-newItem';
				$repeaterItemsOut[] = $newField->output();
			}
		}

		$this->view->repeaterItemsOut = $repeaterItemsOut;
	}
	
}