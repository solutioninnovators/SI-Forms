$(function () {
    init($('.autocompleteField .field-input'));

    $('body').on('ui-reloaded', '.ui', function(e) {
        e.stopPropagation();
       init($(this).find('.autocompleteField .field-input'));
    });

    function init($fields){
        $fields.each(function() {
            var $this = $(this);
            var $realField = $this.siblings('.autocompleteField-value');
            var previousValue = $realField.val();
            var $spinner = $this.siblings('.autocompleteField-spinner');
            var $form = $this.closest('form');

            var defaultSettings = {
                serviceUrl: '',
                dataType: 'json',
                showNoSuggestionNotice: true,
                autoSelectFirst: true,
                preventBadQueries: false, // Having this enabled prevents the ID match from working correctly
                deferRequestBy: 250, // Number of milliseconds to defer Ajax request
                groupBy: 'category',
                // Combine the current query string, current form data, and ajax request info
                params: $.extend(queryStringToJSON(), queryStringToJSON($form.serialize()), { ajax: 'getMatches', ui: $this.closest('.ui').attr('data-ui-path')}),
                onSelect: function (suggestion) {
                    $realField.val(suggestion.data.id); // Copy the selection to the real field

                    if (suggestion.data.id != previousValue) {
                        $realField.trigger('change');
                    }
                    previousValue = suggestion.data.id;
                },
                onSearchStart: function (query) {
                    $spinner.show();
                },
                onSearchComplete: function (query, suggestions) {
                    $spinner.hide();
                },
            };
            var customSettings =  $this.data('settings');

            var settings = $.extend(defaultSettings, customSettings);

            $this.autocomplete(settings);

            // If the user manually types or pastes in a value, copy the value to the hidden field so that we can check it when saving the page
            $this.on('input', UiBlocks.debounce(function (e) {
                $realField.val($this.val());
                if ($this.val() != previousValue) {
                    $realField.trigger('change');
                }
                previousValue = $this.val();
            }, 500));

            // Trigger ui-value-changed event on the UI Block when field is changed
            $realField.on('change', function () {
                var $this = $(this);
                var $ui = $this.closest('.ui');
                $ui.trigger('ui-value-changed', [{value: $this.val()}]);
            });
        });
    }

    function queryStringToJSON(queryString) {
        if(queryString === undefined) {
            queryString = location.search.slice(1);
        }

        var pairs = queryString.split('&');

        var result = {};
        pairs.forEach(function (pair) {
            if(pair[0]) {
                pair = pair.split('=');
                result[pair[0]] = decodeURIComponent(pair[1] || '');
            }
        });

        return JSON.parse(JSON.stringify(result));
    }

});