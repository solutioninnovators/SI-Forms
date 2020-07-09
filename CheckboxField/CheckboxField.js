$(function() {
    $('body').on('click', '.checkboxField .field-input', function() {
        var $this = $(this);
        var $ui = $this.closest('.ui');
        $ui.trigger('ui-value-changed', [{value: $this.val()}]);
    });
});