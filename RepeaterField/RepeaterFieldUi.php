<?php namespace ProcessWire;

/**
 * Class RepeaterFieldUI
 * @package ProcessWire
 * 
 * Takes any other FieldUI and makes it repeatable. The values returned from the individual fields are stored as a multidimensional array
 *
 */
class RepeaterFieldUi extends FieldUi {
	public $itemTemplate; // The field object or array of field objects of type FieldUI to repeat. This should not have a value. Name is optional and will only be used for multiple templates.
	public $value = [];
	public $showBlankItem = true; // Show a new item right away without having to click the "add" button
    public $itemLimit;  // The number of items allowed to be added
	public $cssClass = 'repeaterField';

	public $children = []; // The items (rows) in the repeater

	private $repeaterItemsConstructed = false;
	
	protected function setup() {}

	/**
	 * Constructs the appropriate number of fields (repeater items) and updates their values based on the current $this->value array. Uses $this->fieldTemplate as the template for each repeater item.
	 *
	 * @todo: Process callback options on each repeater item?
	 */
	private function constructRepeaterItems() {
		$repeaterItems = [];
		$repeaterItemIteration = 0;

		foreach($this->value as $repeaterItemValue) {
			if(empty($repeaterItemValue)) continue;

			if(is_array($this->itemTemplate) && is_array($repeaterItemValue)) { // Array of item templates
				
				$subfieldIteration = 0;
				foreach($this->itemTemplate as $subfieldTemplate) {
					$subfield = clone $subfieldTemplate;
					$subfieldName = $subfield->name;
					$subfield->id = $this->id . $repeaterItemIteration . $subfield->id;
					$subfield->name = $this->name . "[$repeaterItemIteration][$subfieldName]";
					$subfield->value = $repeaterItemValue[$subfieldName];
					$repeaterItems[$repeaterItemIteration][$subfieldName] = $subfield;

					$subfieldIteration++;
				}
			}
			else { // Single item template
                $subfield = clone $this->itemTemplate;
				$subfield->id = $this->id . $repeaterItemIteration;
				$subfield->name = $this->name . "[$repeaterItemIteration]";
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
	public function fieldValidate() {
		$valid = true;

		// Reconstruct the fields array (repeater items) based on the new $this->value (if changed)
		/*if($this->value !== $this->initialValue) {
			$this->children = $this->constructRepeaterItems();
		}*/

		if($this->itemLimit && $this->itemLimit < count($this->children)) {
			$this->error = __('Max number of items is ') . $this->itemLimit;
			$valid = false;
		}

		// Loop through each of the child fields and call its validate() function.
		foreach($this->children as $child) { // Multiple fields per row
			if(is_array($child)) {
				foreach($child as $subfield) {
					if(!$subfield->validate()) $valid = false;
				}
			}
			else { // One field per row
				if(!$child->validate()) $valid = false;
			}
		}

		// We can set an error message for the whole repeater field if any of its subfields are invalid, or just let each subfield show its own error
		//if(!$valid) $this->error = __('One or more of the values above is invalid.');


		return $valid;
	}

	protected function run() {
		// In legacy mode the repeater items must be constructed here because afterValueSet() is never called on non-submits
		if($this->form && $this->form->legacyMode && !$this->repeaterItemsConstructed) {
			$this->children = $this->constructRepeaterItems();
			$this->repeaterItemsConstructed = true;
		}

		// Prepare output
		$repeaterItemsOut = [];

		// Create a hidden blank template item for javascript to use to make new items
		// NOTE: If the '$' element is appearing in the array, that means that one of the item template fields probably does not yet support the "disabled" property, so it is getting submitted. The field in question should be fixed.
		if(is_array($this->itemTemplate)) { // Multiple fields per row
			$repeaterRow = '';
			foreach($this->itemTemplate as $fieldTemplate) {
				$newField = clone $fieldTemplate;
                $newField->id = $this->id . '$' . $newField->name;
				$newField->name = $this->name . "[$][$newField->name]";
				$newField->disabled = true;
				$repeaterRow .= $newField->render();
			}
			$repeaterItemsOut[] = $repeaterRow;
		}
		else { // One field per row
			$template = clone $this->itemTemplate;
            $template->id = $this->id . '$';
			$template->name = $this->name . '[$]';
			$repeaterItemsOut[] = $template->render();
		}

		// Render existing fields in $this->fields for the view
		foreach ($this->children as $field) {
			if(is_array($field)) { // Multiple fields per row
				$repeaterRow = '';
				foreach($field as $subfield) {
					$repeaterRow .= $subfield->render();
				}
				$repeaterItemsOut[] = $repeaterRow;
			}
			else { // One field per row
				$repeaterItemsOut[] = $field->render();
			}
		}

		// Create an additional blank field for new input
		if($this->showBlankItem || count($this->children) == 0) {
			$newItemIndex = count($this->children);

			if(is_array($this->itemTemplate)) { // Multiple fields per row
				$repeaterRow = '';
				foreach($this->itemTemplate as $fieldTemplate) {
					$newField = clone $fieldTemplate;
                    $newField->id = $this->id . $newItemIndex . $newField->id;
					$newField->name = $this->name . "[$newItemIndex][$newField->name]";
					$newField->classes = $newField->classes .= ' repeaterField-newItem';
					$repeaterRow .= $newField->render();
				}
				$repeaterItemsOut[] = $repeaterRow;
			}
			else { // One field per row
				$newField = clone $this->itemTemplate;
                $newField->id = $this->id . $newItemIndex;
				$newField->name = $this->name . "[$newItemIndex]";
				$newField->classes = $newField->classes .= ' repeaterField-newItem';
				$repeaterItemsOut[] = $newField->render();
			}
		}

		$this->view->repeaterItemsOut = $repeaterItemsOut;
	}

	public function afterValueSet() {
		$this->children = $this->constructRepeaterItems();
		$this->repeaterItemsConstructed = true;
	}

	/**
	 * A repeater is considered "populated" if at least one of its children is populated
	 */
	protected function isPopulated() {
		
		foreach($this->children as $child) {
			if(is_array($child)) { // Multiple fields per row
				foreach($child as $subfield) {
					if(!$subfield->isPopulated()) return true;
				}
			}
			else { // One field per row
				if(!$child->isPopulated()) return true;
			}
		}

		return false;
	}
	
}