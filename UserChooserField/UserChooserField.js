$(function() {
    function closeDropDown($field) {
        $field.css({'min-width': 'initial', 'min-height': 'initial'});
        $field.removeClass('userChooser_open');

        // Show placeholder if none selected
        if(!$field.find('.userChooser-selected').length) {
            $field.find('.userChooser-placeholder').show();
        }
    }

    function openDropDown($field) {
        $field.closest('.userChooserField').css({'min-width': $field.outerWidth() + 'px', 'min-height': $field.outerHeight() + 'px'}); // Prevent the content from reflowing when the drop down is open
        $field.find('.userChooser-placeholder').hide(); // Hide placeholder
        $field.addClass('userChooser_open');
    }

    $('body').on('click', '.userChooser-item:not(.userChooser-readOnly), .userChooser-placeholder', function() {
        var $this = $(this);
        var $field = $this.closest('.userChooser');
        var useDropDown = $field.hasClass('userChooser_useDropDown');

        if(useDropDown && !$field.hasClass('userChooser_open')) {
            closeDropDown($('.userChooser_open').not($field)); // Close all other drop downs
            openDropDown($field);
        }
        else {
            // If this field is singular, remove all other selections
            if ($field.hasClass('userChooser_singular')) {
                var $otherSelections = $this.siblings('.userChooser-selected');
                $otherSelections.removeClass('userChooser-selected');
                $otherSelections.find('.userChooser-checkbox').attr('checked', false);

                // Close drop down after selection
                if(useDropDown) {
                    closeDropDown(this);
                }
            }

            if ($this.hasClass('userChooser-selected')) {
                $this.find('.userChooser-checkbox').attr('checked', false);
                $this.removeClass('userChooser-selected')
            } else {
                $this.find('.userChooser-checkbox').attr('checked', true);
                $this.addClass('userChooser-selected');
            }

            // Trigger ui-value-changed event
            var valArray = $field.find("input:checked").map(function () {
                return $(this).val();
            }).get();
            $field.closest('.ui').trigger('ui-value-changed', [{value: valArray}]);
        }
    });

    $('body').on('click', '.userChooser', function(event) {
        event.stopPropagation(); // Prevents userChooser from closing when clicking on the field itself.
    });
    $(window).on('click', function(event) {
        closeDropDown($('.userChooser_open'));
    });
});