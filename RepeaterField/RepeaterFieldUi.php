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
	public $showBlankItemWhenEmpty = true;
    public $itemLimit;  // The number of items allowed to be added
	public $cssClass = 'repeaterField';
	public $sortable = false;
    public $noRemove = false;
    public $showLabelOnce = false;
    // determines whether to show a drag handle
	public $showSortHandle = false; // todo: Sorting without the sort handle is currently broken
    // if you provide a custom class here, it will be used to decide what CSS to use for the drag handle
	public $sortHandleClass = 'repeaterField-dragHandle';
	public $innerClasses = 'gGrid'; // CSS classes to add to the inner div wrapper inside the repeater (typically to add grid framework classes)
	public $addNewIcon = '<i class="fa fa-fw fa-plus"></i>';

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

		// Create a hidden blank template item for javascript to use to make new items.
		// NOTE: This item is given an index of -1. It should not post when the form is submitted. If the '-1' index is appearing in POST/GET, then that means that one of the item template fields probably does not yet support the "disabled" property. The field in question should be updated to support this property.
		$this->constructRepeaterItem($repeaterItems, -1);

		// Create one repeater item for each element in the $this->value array
		for($i = 0; $i < count($this->value); $i++) {
			$this->constructRepeaterItem($repeaterItems, $i, $this->value[$i]);
		}

		// Determine if we should show a blank item for new input when the page loads.
		if(!$this->form->isSubmission()) {
			// Always show a blank item if there are no other items and $this->showBlankItemWhenEmpty = true
			if(count($this->value) == 0) {
				if($this->showBlankItemWhenEmpty) {
					$this->constructRepeaterItem($repeaterItems, count($this->value));
				}
			}
			// Only show an additional blank item if $this->showBlankItem is enabled
			elseif($this->showBlankItem) {
				// Skip adding an additional blank item if the last one is already blank
				$lastChild = count($this->value) > 0 ? $repeaterItems[array_key_last($repeaterItems)] : null;
				if($lastChild && $this->childIsPopulated($lastChild)) {
					$this->constructRepeaterItem($repeaterItems, count($this->value));
				}
			}
		}
				
		return $repeaterItems;
	}

	private function constructRepeaterItem(&$repeaterItems, $i, $repeaterItemValue = null) {
		if(is_array($this->itemTemplate)) { // Array of item templates
				foreach($this->itemTemplate as $subfieldTemplate) {
					$subfield = clone $subfieldTemplate;
					$subfieldName = $subfield->name;
				$subfield->id = $this->id . "__{$i}__" . $subfield->id;
				$subfield->name = $this->name . "[$i][$subfieldName]";
				if($i === -1) $subfield->disabled = true;
				if($repeaterItemValue) $subfield->value = $repeaterItemValue[$subfieldName];
				$subfield->iteration = $i;
					$subfield->form = $this->form;
				$subfield->parent = $this;
					$subfield->afterValueSet();
				$repeaterItems[$i][$subfieldName] = $subfield;
				}
			}
			else { // Single item template
                $subfield = clone $this->itemTemplate;
			$subfield->id = $this->id . "__{$i}__";
			$subfield->name = $this->name . "[$i]";
			if($i === -1) $subfield->disabled = true;
			if($repeaterItemValue) $subfield->value = $repeaterItemValue;
			$subfield->iteration = $i;
				$subfield->form = $this->form;
			$subfield->parent = $this;
				$subfield->afterValueSet();
				$repeaterItems[] = $subfield;
			}
	}

	/**
	 * @return bool
	 *
	 * The calling form will have already set $this->value to the new value coming from post or get, so we just have to check the current value of $this->value
	 */
	public function fieldValidate() {
		$valid = true;

		if($this->itemLimit && $this->itemLimit < count($this->value)) {
			$this->error = __('Max number of items is ') . $this->itemLimit;
			$valid = false;
		}

		// Loop through each of the child fields and call its validate() function.
		foreach($this->children as $key => $child) {
			if($key === -1) continue; // Don't validate the template item

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

		// Render output
		$repeaterItemsOut = [];
		foreach ($this->children as $field) {
				if (is_array($field)) { // Multiple fields per row
					$repeaterRow = '';
					foreach ($field as $subfield) {
						$repeaterRow .= $subfield->render();
					}
					$repeaterItemsOut[] = $repeaterRow;
				} else { // One field per row
					$repeaterItemsOut[] = $field->render();
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
				if($subfield instanceof FieldUi && $subfield->isPopulated() && !$subfield->nonNull)
					return true;
			}
		}
		elseif($child instanceof FieldUi && $child->isPopulated() && !$child->nonNull) { // Child is a single field
			return true;
		}
		return false;
	}
	
}