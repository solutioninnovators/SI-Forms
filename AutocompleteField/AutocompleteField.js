$(function() {
   $('.autocompleteField .field-input').each(function() {
       var $this = $(this);
       var $realField = $this.siblings('.autocompleteField-value');
       var previousValue = $realField.val();
       var $spinner = $this.siblings('.autocompleteField-spinner');

       $this.autocomplete({
           serviceUrl: '',
           dataType: 'json',
           showNoSuggestionNotice: true,
           autoSelectFirst: true,
           preventBadQueries: false, // Having this enabled prevents the ID match from working correctly
           deferRequestBy: 250, // Number of milliseconds to defer Ajax request
           params: { ajax: 'getMatches', ui: $this.closest('.ui').attr('data-ui-path') },
           onSelect: function(suggestion) {
               $realField.val(suggestion.data); // Copy the selection to the real field

               if(suggestion.data != previousValue) {
                   $realField.trigger('change');
               }
               previousValue = suggestion.data;
           },
           onSearchStart: function(query) {
               $spinner.show();
           },
           onSearchComplete: function(query, suggestions) {
               $spinner.hide();
           },
       });

       // If the user manually types or pastes in a value, copy the value to the hidden field so that we can check it when saving the page
       $this.on('input', debounce(function(e) {
            $realField.val($this.val());
            if($this.val() != previousValue) {
                $realField.trigger('change');
            }
            previousValue = $this.val();
       }, 500));
   });
});