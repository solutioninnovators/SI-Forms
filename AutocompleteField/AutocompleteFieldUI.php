<?php namespace ProcessWire;

/**
 * Class AutocompleteFieldUI
 * Provides an AJAX autocomplete for selecting a single processwire page when you have a large number of pages to choose from. Also accepts a page ID entered manually.
 *
 * If $this->allowStringValue is set to true, will accept any text string, sanitize it, and set it back to $this->value. Otherwise will set $this->value to the page id of the selected Page
 *
 *
 */

class AutocompleteFieldUI extends FieldUI {
	public $searchFields = 'title'; // Pipe separated list of field names to search
	public $searchById = true; // Show matching result in the autocomplete when a valid page ID is entered directly
	public $searchSelector; // A selector string to narrow down the pages that will be searched
	public $validateSelector; // Optional selector string to validate the user's input. If not specified, the $searchSelector will be used. Use this when you want to allow certain values but don't want them to show in the autocomplete search results.
	public $resultsLabel = 'title'; // Field name to use for the result label
	public $resultsLabelString; // Use for complex labels with multiple data fields. Specify one or more field names surrounded by curly {brackets} along with any additional characters, spacing or punctuation.
	public $allowStringValue = false; // Allows the field to accept an arbitrary string value as an alternative to a page ID
	public $resultLimit = 10;
	public $searchOperator = '%='; // ProcessWire search operator to use to match results

	protected function setup() {
		$this->headScripts[] = $this->config->urls->templates . 'library/jquery.autocomplete.js'; // This is not the same as jQueryUI Autocomplete. See https://github.com/devbridge/jQuery-Autocomplete
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
	}

	private function buildSearchSelector($query) {
		$query = str_replace('"', '', $query); // Remove any existing double quotes from the input
		$query = "\"$query\""; // Wrap the string in double quotes to prevent injection attacks

		$selector = "{$this->searchSelector}, limit={$this->resultLimit}, q=({$this->searchFields}{$this->searchOperator}{$query})";
		if($this->searchById) $selector .= ",q=(id={$query})";

		return $selector;
	}

	protected function buildValidateSelector() {
		$validateSelector = $this->validateSelector ?: $this->searchSelector;

		return "id=" . (int)$this->value . ',' . $validateSelector;
	}

	protected function fieldValidate() {
		if($this->value == '') {
			if($this->required == false) return true;
			else $this->error = 'Required.';
		}
		else {
			if($this->allowStringValue == false) {
				// Make sure value is an ID that matches a valid page for this field
				if(!$this->pages->get($this->buildValidateSelector())->id) {
					$this->error = 'Invalid selection.';
					$this->view->displayValue = $this->value;
				}
			}
			else {
				$this->value = $this->sanitizer->text($this->value);
			}
		}

		if($this->error) return false;
		else return true;
	}

	protected function createLabel(Page $match) {
		if(!$this->resultsLabelString) {
			$label = $match->{$this->resultsLabel};
		}
		else {
			$label = $match->getMarkup($this->resultsLabelString);
		}

		return $this->sanitizer->unentities($label); // We're encoding in the view. jQuery Autocomplete also does its own encoding, so this avoids double encoding.
	}

	/**
	 * Returns matching results (Pages) for the autocomplete jquery plugin
	 */
	protected function ajax_getMatches() {
		$query = $this->input->get->query;

		if($query) {
			$matches = $this->pages->find($this->buildSearchSelector($query));

			$this->ajax['suggestions'] = [];
			foreach($matches as $match) {
				$label = $this->createLabel($match);

				$this->ajax['suggestions'][] = ['value' => $label, 'data' => "$match->id"];
			}
		}
	}
}