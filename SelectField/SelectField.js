/**
 * @todo: prevent chosen call if not using autocomplete.
 */
$(function() {
    function init() {
        var autocomplete = $('.selectField-autocomplete');

        if(autocomplete.length > 0) {
            $('.selectField-autocomplete').chosen({
                width: "100%"
            });
        }
    }

    init();

    // re-initialize on reload
	$('body').on('ui-reloaded', '*', function(e) {
		init();
	});

    $('body').on('change', '.selectField .field-input', function() {
        var $this = $(this);
        var $ui = $this.closest('.ui');
        $ui.trigger('ui-value-changed', [{value: $this.val()}]);
    });
});