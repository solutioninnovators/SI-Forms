$(function() {
    /**
     * Submit the entire form via ajax
     */
    $('body').on('submit', 'form[data-ajax-submit="1"]', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $ui = $form.closest('.ui');

        $.ajax({
            type: $form.attr('method'),
            url: $ui.closest('.ui[data-ui-url]').attr('data-ui-url'), // Use url from the closest UI block with the data-ui-url attribute set, otherwise use current page url
            dataType: 'json',
            data: $form.serialize() + '&' + $.param({ui: $ui.attr('data-ui-path'), ajax: 'submit'}),
            success: function(data) {
                $ui.trigger('submitted', [data]); // Allow other js to pick up on the submitted event

                $form.find('.field-error').hide();
                $form.find('.field-message').hide();

                var $csrfInputs = $('._post_token');
                $csrfInputs.attr('name', data.csrfName);
                $csrfInputs.val(data.csrfValue);


                if(data.fieldData) {

                    for(var key in data.fieldData) {
                        var value = data.fieldData[key];

                        var $field = $form.find('[data-ui-id=' + key + ']');

                        if(value.error){
                            var $errorElement = $field.find('.field-error').last();

                            $errorElement.find('.field-errorTxt').html(value.error);
                            $errorElement.fadeIn();
                            $field.trigger('error',[value.error]); // Allow other js to pick up on the error event
                        }

                        if(value.message){
                            var $messageElement = $field.find('.field-message');

                            $messageElement.find('.field-messageTxt').html(value.message);
                            $messageElement.fadeIn();
                            $field.trigger('message',[value.message]); // Allow other js to pick up on the error event
                        }
                    }
                }

                if(data.error) {
                    if(data.error != 'Please fix the errors indicated below and resubmit the form.') alert(data.error); // @todo: This is a really hacky way to only show the messages we want to
                    $ui.trigger('error', [data]); // Allow other js to pick up on the error event
                }
                else {
                    $ui.trigger('success', [data]); // Allow other js to pick up on the success event
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                alert('Form could not be submitted due to a network error. You may have been logged out due to inactivity. Please reload the page and try again.');
                console.log('Error: ' + textStatus + ' ' + errorThrown);
            }
        });
    });


    /**
     * Submit (Save and/or validate) an individual field when its value changes, within the context of the larger form
     */
    //@todo: combine the field and ui wrappers
    $('body').on('valueChange', '.ui', function(e, params) {
        e.stopPropagation();

        var $fieldUI = $(this);
        var $field = $fieldUI.find('.field');

        // @todo: Change to data-ajax-submit?
        if(!$field.attr('data-ajax-save') && !$field.attr('data-ajax-validate')) return;

        var $form = $fieldUI.closest('form');
        var $ui = $form.closest('.ui');
        var $saveBadge = $field.find('.field-saveBadge');
        var $spinner = "<i class='fas fa-spin fa-spinner'></i>";
        var $checkmark = "<i class='fas fa-check-circle'></i>";
        var saveBadgeTimeout = null;
        var timestamp = new Date().getTime();
        var value = params.value;
        var fieldName = $fieldUI.attr('data-ui-id');

        // Set the time of this ajax call
        $field.attr('data-ajax-timestamp', timestamp);

        $saveBadge.html($spinner).fadeIn();
        if (saveBadgeTimeout != null) clearTimeout(saveBadgeTimeout);

        $.ajax({
            type: $form.attr('method'),
            url: $ui.closest('.ui[data-ui-url]').attr('data-ui-url'), // Use url from the closest UI block with the data-ui-url attribute set, otherwise use current page url
            dataType: 'json',
            data: $form.serialize() + '&' + $.param({ui: $ui.attr('data-ui-path'), ajax: 'submitField', timestamp: timestamp, field: fieldName}),
            success: function(data) {
                if(timestamp == $field.attr('data-ajax-timestamp')) { // Only show results of the most recent ajax call
                    if (data.saved) {
                        $fieldUI.trigger('validated', [value]);
                        $fieldUI.trigger('saved', [value]);
                        console.log('Save successful.');

                        $saveBadge.html($checkmark);
                        $field.find('.field-error').fadeOut();
                        $field.find('.field-message').fadeOut();

                        saveBadgeTimeout = setTimeout(function () {
                            $saveBadge.fadeOut(1000);
                        }, 3000);
                    }
                    else if (data.error) {
                        console.log('Invalid input.');

                        $saveBadge.fadeOut();
                        $field.find('.field-errorTxt').html(data.error);
                        $field.find('.field-error').fadeIn();

                        $fieldUI.trigger('error', [data.error]); // Allow other js to pick up on the error event
                    }
                    else { // Value was validated (but not saved)
                        $fieldUI.trigger('validated', [value]);

                        $field.find('.field-error').fadeOut();
                        $saveBadge.fadeOut();
                    }

                    if (data.message) {
                        $field.find('.field-messageTxt').html(data.message);
                        $field.find('.field-message').fadeIn()

                        $fieldUI.trigger('message', [data.message]); // Allow other js to pick up on the message event
                    } else {
                        $field.find('.field-message').fadeOut();
                    }

                }
                else {
                    console.log('AJAX response ignored because it was obsolete');
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                $saveBadge.fadeOut();
                alert('Network error. You may have been logged out due to inactivity or a lost connection. Please reload the page and try again.');
                console.log('Error: ' + textStatus + ' ' + errorThrown);
            }
        });
    });
    
});