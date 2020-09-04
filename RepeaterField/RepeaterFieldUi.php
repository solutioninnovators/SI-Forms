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
	public $sortable = false;
	//public $showSortHandle = false;

	public $children = []; // The items (rows) in the repeater

	private $repeaterItemsConstructed = false;
	
	protected function setup() {
		$this->headScripts[] = $this->url . 'Sortable.min.js';
	}

	/**
	 * Constructs the appropriate number of fields (repeater items) and updates their values based on the current $this->value array. Uses $this->fieldTemplate as the template for each repeater item.
	 *
	 */
	private function constructRepeaterItems() {
		$repeaterItems = [];
		$repeaterItemIteration = 0;

		foreach($this->value as $repeaterItemValue) {
			if(is_array($this->itemTemplate) && is_array($repeaterItemValue)) { // Array of item templates
				
				$subfieldIteration = 0;
				foreach($this->itemTemplate as $subfieldTemplate) {
					$subfield = clone $subfieldTemplate;
					$subfieldName = $subfield->name;
					$subfield->id = $this->id . "__{$repeaterItemIteration}__" . $subfield->id;
					$subfield->name = $this->name . "[$repeaterItemIteration][$subfieldName]";
					$subfield->value = $repeaterItemValue[$subfieldName];
					$subfield->iteration = $repeaterItemIteration;
					$subfield->form = $this->form;
					$subfield->afterValueSet();
					$repeaterItems[$repeaterItemIteration][$subfieldName] = $subfield;

					$subfieldIteration++;
				}
			}
			else { // Single item template
                $subfield = clone $this->itemTemplate;
				$subfield->id = $this->id . "__{$repeaterItemIteration}__";
				$subfield->name = $this->name . "[$repeaterItemIteration]";
				$subfield->value = $repeaterItemValue;
				$subfield->iteration = $repeaterItemIteration;
				$subfield->form = $this->form;
				$subfield->afterValueSet();
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

		if($this->itemLimit && $this->itemLimit < count($this->children)) {
			$this->error = __('Max number of items is ') . $this->itemLimit;
			$valid = false;
		}

		// Loop through each of the child fields and call its validate() function.
		foreach($this->children as $child) {
			if(is_array($child)) { // Multiple fields per row
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
                $newField->id = $this->id . '__$__' . $newField->name;
				$newField->name = $this->name . "[$][$newField->name]";
				$newField->disabled = true;
				$repeaterRow .= $newField->render();
			}
			$repeaterItemsOut[] = $repeaterRow;
		}
		else { // One field per row
			$template = clone $this->itemTemplate;
            $template->id = $this->id . '__$__';
			$template->name = $this->name . '[$]';
			$template->disabled = true;
			$repeaterItemsOut[] = $template->render();
		}

		// Render existing fields in $this->fields for the view
		foreach ($this->children as $field) {
			//if($field->isPopulated()) { // Filter out unpopulated fields
				if (is_array($field)) { // Multiple fields per row
					$repeaterRow = '';
					foreach ($field as $subfield) {
						$repeaterRow .= $subfield->render();
					}
					$repeaterItemsOut[] = $repeaterRow;
				} else { // One field per row
					$repeaterItemsOut[] = $field->render();
				}
			//}
		}

		// Create an additional blank field for new input
		$totalChildren = count($this->children);
		if($this->showBlankItem || $totalChildren == 0) {
			// Don't create an additional element if the last child is already unpopulated
			$lastChild = $totalChildren > 0 ? $this->children[$totalChildren - 1] : null;
			if($totalChildren < 1 || ($lastChild && $this->childIsPopulated($lastChild))) {

				$newItemIndex = $totalChildren;

				if (is_array($this->itemTemplate)) { // Multiple fields per row
					$repeaterRow = '';
					foreach ($this->itemTemplate as $fieldTemplate) {
						$newField = clone $fieldTemplate;
						$newField->id = $this->id . "__{$newItemIndex}__" . $newField->id;
						$newField->name = $this->name . "[$newItemIndex][$newField->name]";
						$newField->classes = $newField->classes .= ' repeaterField-newItem';
						$repeaterRow .= $newField->render();
					}
					$repeaterItemsOut[] = $repeaterRow;
				}
				else { // One field per row
					$newField = clone $this->itemTemplate;
					$newField->id = $this->id . "__{$newItemIndex}__";
					$newField->name = $this->name . "[$newItemIndex]";
					$newField->classes = $newField->classes .= ' repeaterField-newItem';
					$repeaterItemsOut[] = $newField->render();
				}
			}
		}

		$this->view->repeaterItemsOut = $repeaterItemsOut;

		return parent::run();
	}

	public function afterValueSet() {
		$this->children = $this->constructRepeaterItems();
		$this->repeaterItemsConstructed = true;
	}

	/**
	 * A repeater is considered "populated" if at least one of its children is populated
	 */
	public function isPopulated() {
		foreach($this->children as $child) {
			if($this->childIsPopulated($child))
				return true;
		}
		return false;
	}

	public function childIsPopulated($child) {
		if(is_array($child)) { // Child is an array of fields
			foreach($child as $subfield) {
				if($subfield instanceof FieldUi && $subfield->isPopulated())
					return true;
			}
		}
		elseif($child instanceof FieldUi) { // Child is a single field
			return $child->isPopulated();
		}
		return false;
	}
	
}