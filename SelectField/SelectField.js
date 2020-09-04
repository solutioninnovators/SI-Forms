$(function() {
    function init($fields) {
        if($fields.length > 0) {
            $fields.chosen({
                width: "100%"
            });
        }
    }

    init($('.selectField-autocomplete'));

    // re-initialize on reload
	$('body').on('ui-reloaded', '.ui', function(e) {
        e.stopPropagation();
		init($(this).find('.selectField-autocomplete'));
	});

    $('body').on('change', '.selectField .field-input', function() {
        var $this = $(this);
        var $ui = $this.closest('.ui');
        $ui.trigger('ui-value-changed', [{value: $this.val()}]);
    });
});