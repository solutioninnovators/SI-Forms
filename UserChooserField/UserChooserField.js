$(function() {
    $('.userChooser-item').not('.userChooser-readOnly').on('click', function() {
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
    });
});