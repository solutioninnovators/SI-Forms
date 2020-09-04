$(function() {
    $('body').on('click', '.userChooser-item:not(.userChooser-readOnly)', function() {
        var $this = $(this);
        var $field = $this.closest('.userChooser');

        // If this field is singular, remove all other selections
        if($field.hasClass('userChooser_singular')) {
            var $otherSelections = $this.siblings('.userChooser-selected');
            $otherSelections.removeClass('userChooser-selected');
            $otherSelections.find('.userChooser-checkbox').attr('checked', false);
        }

        if($this.hasClass('userChooser-selected')) {
            $this.find('.userChooser-checkbox').attr('checked', false);
            $this.removeClass('userChooser-selected')
        }
        else {
            $this.find('.userChooser-checkbox').attr('checked', true);
            $this.addClass('userChooser-selected');
        }

        // Trigger ui-value-changed event
        var valArray = $field.find("input:checked").map(function() { return $(this).val(); }).get();
        $field.closest('.ui').trigger('ui-value-changed', [{value: valArray}]);
    });
});