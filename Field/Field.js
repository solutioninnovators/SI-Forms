$(function() {
    /**
     * Rules to trigger a valueChange event
     * @todo: Move these to the individual fields
     */
    $('body').on('change', '.selectField .field-input', function() {
        //console.log('Field changed.');

        var $this = $(this);
        var $ui = $this.closest('.ui');
       $ui.trigger('valueChange', [{value: $this.val()}]);
    });

    $('body').on('input', '.textareaField .field-input, .textField .field-input', debounce(function(e) {
        //console.log('Field changed.');

        var $this = $(this);
        var $ui = $this.closest('.ui');
        $ui.trigger('valueChange', [{value: $this.val()}]);
    }, 500));


    $('body').on('click', '.checkboxField .field-input', function() {
        //console.log('Field changed.');

        var $this = $(this);
        var $ui = $this.closest('.ui');
        $ui.trigger('valueChange', [{value: $this.val()}]);
    });
});