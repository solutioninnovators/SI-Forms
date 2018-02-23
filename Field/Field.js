$(function() {
    /**
     * AJAX save a field
     */
    $('body').on('save', '.field', function(e, params) {
        console.log('Initiating save...');

        var $field = $(this);
        if(!$field.attr('data-ajax-save')) return;
        var $ui = $(this).closest('.ui');
        var $saveBadge = $field.find('.field-saveBadge');
        var $spinner = "<i class='fa fa-spin fa-circle-o-notch'></i>";
        var $checkmark = "<i class='fa fa-check-circle'></i>";
        var saveBadgeTimeout = null;

        $saveBadge.html($spinner).fadeIn();
        if(saveBadgeTimeout != null) clearTimeout(saveBadgeTimeout);

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {ui: $ui.attr('data-ui-id'), ajax: 'save', value: params.value},
            success: function(data) {
                if(data.saved) {
                    console.log('Save successful.');

                    $saveBadge.html($checkmark);
                    $field.find('.field-error').fadeOut();

                    saveBadgeTimeout = setTimeout(function() {
                        $saveBadge.fadeOut(1000);
                    }, 3000);
                }
                else {
                    console.log('Save failed due to validation error.');

                    $saveBadge.fadeOut();
                    $field.find('.field-errorTxt').text(data.error);
                    $field.find('.field-error').fadeIn();
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                $saveBadge.fadeOut();
                alert('Field could not be saved. You may have been logged out due to inactivity.');
                console.log('Error: ' + textStatus + ' ' + errorThrown);
            }
        });
    });

    /**
     * Rules to trigger a field save event
     */
    $('body').on('change', '.textareaField .field-input, .textField .field-input', function() {
        console.log('Field changed.');

        var $field = $(this).closest('.field');
        if(!$field.attr('data-ajax-save')) return;

        $field.trigger('save', [{value: $(this).val()}]);
    });

    $('body').on('click', '.checkboxField .field-input', function() {
        console.log('Field changed.');

        var $field = $(this).closest('.field');
        if(!$field.attr('data-ajax-save')) return;

        $field.trigger('save', [{value: $(this).val()}]);
    });
});