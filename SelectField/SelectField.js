$(function() {
    function init($fields) {
        if($fields.length > 0) {
            $fields.chosen({
                width: "100%",
                search_contains: true,
            });
        }
    }

    init($('.selectField-autocomplete'));

    // re-initialize on reload
	$('body').on('ui-reloaded', '.ui', function(e) {
        e.stopPropagation();
        // The auto-complete select field using chosen.js will duplicate in the init call.
        // There is a disabled "original" one that we delete here.
        (($(this).closest('.repeaterField-field')).find('.chosen-disabled')).remove();
		init($(this).find('.selectField-autocomplete'));
	});

    $('body').on('change', '.selectField .field-input', function() {
        var $this = $(this);
        var $ui = $this.closest('.ui');
        $ui.trigger('ui-value-changed', [{value: $this.val()}]);
    });
});