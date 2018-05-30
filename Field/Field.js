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
            url: $ui.closest('.ui[data-ui-url]').attr('data-ui-url'), // Use url from the closest UI block with the data-ui-url attribute set, otherwise use current page url
            dataType: 'json',
            data: {ui: $ui.attr('data-ui-path'), ajax: 'save', value: params.value},
            success: function(data) {
                if(data.saved) {
                    $ui.trigger('saved');
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
    $('body').on('change', '.selectField .field-input', function() {
        //console.log('Field changed.');

        var $field = $(this).closest('.field');
        if(!$field.attr('data-ajax-save')) return;

        $field.trigger('save', [{value: $(this).val()}]);
    });

    $('body').on('input', '.textareaField .field-input, .textField .field-input', debounce(function(e) {
        //console.log('Field changed.');

        var $field = $(this).closest('.field');
        if(!$field.attr('data-ajax-save')) return;

        $field.trigger('save', [{value: $(this).val()}]);
    }, 1000));


    $('body').on('click', '.checkboxField .field-input', function() {
        //console.log('Field changed.');

        var $field = $(this).closest('.field');
        if(!$field.attr('data-ajax-save')) return;

        $field.trigger('save', [{value: $(this).val()}]);
    });
});