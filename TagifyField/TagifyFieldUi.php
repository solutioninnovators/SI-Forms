<?php namespace ProcessWire;

/**
 * Class TagifyFieldUi
 *
 * To specify the "whitelist" of options:
 *
 * $settings['whitelist'] = [
 * 	['value' => 'Jack', 'id' => 123],
 * 	['value' => 'Jill', 'id' => 456],
 * ]
 *
 * Where the "value" is what will appear on the tag and the "id" is what will be sent to the server (Page ID, for example). This is sort of backwards, since the ID is the real value in terms of the html, but that's just how Tagify does it.
 *
 */

class TagifyFieldUi extends FieldUi {
	public $cssClass = 'tagifyField';
	public $maxLength;
	public $__settings = []; // Array of settings for configuring the tagify instance

	protected function setup() {
		$this->styles[] = $this->url . 'tagify/tagify.css';
		$this->footScripts[] = $this->url . 'tagify/tagify.min.js';
	}

	protected function run() {
		// Set the initial display value when the field loads. If the value is a valid page, format the display value (results label) according to our preferences.
		$this->view->displayValue = $this->value;

		if(ctype_digit($this->value)) {
			$match = $this->pages->get($this->buildValidateSelector());
			if($match->id) { // Valid Page
				$this->view->displayValue = $this->createLabel($match);
			}
		}
		
		return parent::run();
	}

	private function buildSearchSelector($query) {
		$query = str_replace('"', '', $query); // Remove any existing double quotes from the input
		$query = "\"$query\""; // Wrap the string in double quotes to prevent injection attacks

		if($this->searchSelectorCallback) {
			$selector = call_user_func_array($this->searchSelectorCallback, [$query, $this, $this->form]);
		}
		else {
			$selector = "{$this->searchSelector}, limit={$this->resultLimit}, q=({$this->searchFields}{$this->searchOperator}{$query})";
			if($this->searchById) $selector .= ",q=(id={$query})";
		}

		return $selector;
	}

	protected function buildValidateSelector() {
		$validateSelector = $this->validateSelector ?: $this->searchSelector;

		return "id=" . (int)$this->value . ',' . $validateSelector;
	}

	protected function fieldValidate() {

		if($this->error) return false;
		else return true;
	}
}