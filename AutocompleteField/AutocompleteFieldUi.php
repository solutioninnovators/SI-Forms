<?php namespace ProcessWire;

/**
 * Class AutocompleteFieldUi
 * Provides an AJAX autocomplete for selecting a single processwire page when you have a large number of pages to choose from. Also accepts a page ID entered manually.
 *
 * If $this->allowStringValue is set to true, will accept any text string, sanitize it, and set it back to $this->value. Otherwise will set $this->value to the page id of the selected Page
 *
 *
 */

class AutocompleteFieldUi extends FieldUi {
	public $cssClass = 'autocompleteField';
	public $settings = []; // Array of settings for configuring the jquery.autocomplete.js instance.
	public $searchCallback; // A callback function that returns search results. Can be used in place of searchSelector if you're not working with ProcessWire Pages or need absolute control. The callback is passed three parameters: $query, $field, $form. The return value should be an array of associative arrays with a 'value' and a 'data' element for each. The 'data' element is itself an associative array with an 'id' and optional 'category'. See jquery.autocomplete documentation for more options. If using this option, you will also need to provide a $validateCallback to validate the input. Using this $searchCallback overrides most of the options below.
	public $__displayValue; // If using $searchCallback and populating the field with an initial $value, use $displayValue to show a string different from the value in the input box. For example, showing a customer name instead of the customer id.

	// The options below are only relevant if this field is returning ProcessWire Pages and you are not using $searchCallback above
	public $searchFields = 'title'; // Pipe separated list of field names to search
	public $searchById = true; // Show matching result in the autocomplete when a valid page ID is entered directly
	public $__searchSelector; // A selector string to narrow down the pages that will be searched
	public $searchSelectorCallback; // For more complex scenarios. This is a custom callback function that is passed the $query and returns a selector string. Overrides $searchSelector and $searchById
	public $__validateSelector; // Optional selector string to validate the user's input. If not specified, the $searchSelector will be used. Use this when you want to allow certain values but don't want them to show in the autocomplete search results.
	public $resultsLabel = 'title'; // Field name to use for the result label
	public $resultsLabelString; // Use for labels that require multiple data fields. Specify one or more field names surrounded by curly {brackets} along with any additional characters, spacing or punctuation.
	public $resultsLabelCallback; // For more complex label requirements, provide a custom callback function. Your function will be passed a $page representing the result and should return a string representing the resulting label.
    public $allowStringValue = false; // Allows the field to accept an arbitrary string value as an alternative to a page ID
	public $resultLimit = 10;
	public $searchOperator = '%='; // ProcessWire search operator to use to match results
	public $groupBy; // Name of ProcessWire field to group the suggestions by (if grouping the suggestions is desired)
    public $maxLength = 255;

	protected function setup() {
		$this->headScripts[] = $this->url . 'jquery.autocomplete.js'; // This is not the same as jQueryUI Autocomplete. See https://github.com/devbridge/jQuery-Autocomplete
	}

	protected function run() {
		// Set the initial display value when the field loads. If the value is a valid page, format the display value (results label) according to our preferences.
		$this->view->displayValueOut = $this->displayValue ?? $this->value;

		if(!$this->searchCallback) {
			if(ctype_digit($this->value)) {
				$match = $this->pages->get($this->buildValidateSelector());
				if($match->id) { // Valid Page
					$this->view->displayValueOut = $this->createLabel($match);
				}
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
		if(!$this->searchCallback) {
			if($this->allowStringValue == false || ctype_digit($this->value)) {
				// Make sure value is an ID that matches a valid page for this field
				if(!$this->pages->get($this->buildValidateSelector())->id) {
					$this->error = __('Invalid selection.');
					$this->view->displayValueOut = $this->value;
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
		if($this->resultsLabelCallback) {
			$label = call_user_func_array($this->resultsLabelCallback, [$match]);
		}
		elseif($this->resultsLabelString) {
			$label = $match->getMarkup($this->resultsLabelString);
		}
		else {
			$label = $match->{$this->resultsLabel};
		}

		return $this->sanitizer->unentities($label); // We're encoding in the view. jQuery Autocomplete also does its own encoding, so this avoids double encoding.
	}

	/**
	 * Returns matching results (e.g. Pages) for the autocomplete jQuery plugin
	 *
	 * Each result is an associative array with a 'value' and a 'data' element. The 'data' element is an associative array with an 'id' and optional 'category'. See jquery.autocomplete documentation for more.
	 */
	protected function ajax_getMatches() {
		$query = $this->input->get->query;

		if($query) {
			if($this->searchCallback) {
				$this->ajax['suggestions'] = call_user_func_array($this->searchCallback, [$query, $this, $this->form]);
			}
			else {
				$matches = $this->pages->find($this->buildSearchSelector($query));

				$this->ajax['suggestions'] = [];
				if($matches->count()) {
					foreach($matches as $match) {
						$label = $this->createLabel($match);

						$suggestion = ['value' => $label, 'data' => ['id' => "$match->id"]];
						if($this->groupBy) {
							$suggestion['data']['category'] = $match->get("{$this->groupBy}");
						}

						$this->ajax['suggestions'][] = $suggestion;
					}
				}
			}
		}
	}
}