$(function() {
    $('body').on('input', '.textField .field-input', UiBlocks.debounce(function(e) {
        var $this = $(this);
        var $ui = $this.closest('.ui');
        $ui.trigger('ui-value-changed', [{value: $this.val()}]);
    }, 500));
});