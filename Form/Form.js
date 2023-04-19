$(function() {

    /**
     * Get the form this field belongs to. If no form attribute is specified on the field, find the closest form element containing the field.
     * @param $field - Either the ui wrapper or the outer field div jQuery object
     */
    function getFormFromField($field) {
        var $input = $field.find('.field-input');
        var $form = [];

        if($input.length) {
            var formEl = $input[0].form;

            if($('form#' + formEl.id).length > 1) {
                console.log('There is more than one form with the id of "' + formEl.id + '". This id should be made unique.');
            }

            $form = $(formEl);
        }

        if(!$form.length) {
            $form = $field.closest('form');
        }

        return $form;
    }

    /**
     * Prevent form from submitting if $noSubmit is set to true
     */
    $('body').on('submit', 'form[data-no-submit="1"]', function(e) {
        e.preventDefault();
    });

    /**
     * Submit the entire form via ajax
     */
    $('body').on('submit', 'form[data-ajax-submit="1"]', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $ui = $form.closest('.ui_Form');

        $.ajax({
            type: $form.attr('method'),
            url: $ui.closest('.ui[data-ui-url]').attr('data-ui-url'), // Use url from the closest UI block with the data-ui-url attribute set, otherwise use current page url
            dataType: 'json',
            data: $form.serialize() + '&' + $.param({ui: $ui.attr('data-ui-path'), ajax: 'submit'}),
            success: function(data) {
                $ui.trigger('ui-submitted', [data]); // Allow other js to pick up on the submitted event

                $form.find('.field-error').hide();
                $form.find('.field-message').hide();

                var $csrfInputs = $('._post_token');
                $csrfInputs.attr('name', data.csrfName);
                $csrfInputs.val(data.csrfValue);


                if(data.fieldData) {

                    for(var key in data.fieldData) {
                        var value = data.fieldData[key];

                        var $field = $form.find('.field[data-field-name="' + key + '"]');
                        var $fieldUi = $field.closest('.ui');

                        if(value.error){
                            var $errorElement = $field.find('.field-error').last();

                            $errorElement.find('.field-errorTxt').html(value.error);
                            $errorElement.fadeIn();
                            $fieldUi.trigger('ui-error',[value.error]); // Allow other js to pick up on the error event
                        }

                        if(value.message){
                            var $messageElement = $field.find('.field-message');

                            $messageElement.find('.field-messageTxt').html(value.message);
                            $messageElement.fadeIn();
                            $fieldUi.trigger('ui-message',[value.message]); // Allow other js to pick up on the error event
                        }
                    }
                }

                if(!data.success) {
                    $ui.trigger('ui-error', [data]); // Allow other js to pick up on the error event
                }
                else {
                    $ui.trigger('ui-success', [data]); // Allow other js to pick up on the success event

                    // Reset changed class
                    if($form.attr('data-track-unsaved-changes')) {
                        $ui.find('field').removeClass('field_changed');
                    }
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                alert('Form could not be submitted due to a network error. You may have been logged out due to inactivity. Please reload the page and try again.');
                console.log('Error: ' + textStatus + ' ' + errorThrown);
            }
        });
    });

    /**
     * When a field's value changes, reload any of the fields that depend on it, while preserving their values
     */
    $('body').on('ui-value-changed', '.ui', function(e, params) {
        var $field = $(this).find('.field');
        var fieldName = $field.attr('data-field-name');
        var $form = getFormFromField($field);

        // Find all the fields that depend on this one
        var $fields = $(".field[data-depends-on~='" + fieldName + "']");

        if($fields.length) {
            var fieldNames = [];
            $fields.each(function() {
                fieldNames.push($(this).attr('data-field-name'));
            });

            var extraParams = {
                fieldNames: fieldNames
            };

            // Add the form fields to the extra parameters and turn all parameters into a query string
            extraParams = $form.serialize() + '&' + $.param(extraParams);

            // Trigger a reloading event for each of the fields for others to pick up on
            $fields.each(function() {
                $(this).closest('.ui').trigger('ui-reloading');
                $(this).css({opacity: 1, 'pointer-events': 'none'}).animate({opacity: 0.5}, 300);
            });

            // Get the view for each of the fields we want to reload and plug them in where they belong
            UiBlocks.ajax($form.closest('.ui'), 'reloadFields', extraParams, $form.attr('method'), $form.closest('.ui[data-ui-url]').attr('data-ui-url')).then(function(data) {
                $fields.each(function() {
                    var $newView = $(data.views[$(this).attr('data-field-name')]);
                    $(this).closest('.ui').replaceWith($newView);
                    $newView.css({opacity: 0.5}).animate({opacity: 1}, 100);
                    $newView.trigger('ui-reloaded'); // Trigger a reloaded event when the ui is reloaded
                });
            },
            function() {
                console.log('Could not reload dependant fields due to a network error.');
            });
        }
    });

    /**
     * Submit (Save and/or validate) an individual field when its value changes, within the context of the larger form
     */
    //@todo: combine the field and ui wrappers or trigger events on both?
    $('body').on('ui-value-changed', '.ui', function(e, params) {
        e.stopPropagation();

        var $fieldUi = $(this);
        var $field = $fieldUi.find('.field');

        // @todo: Change to data-ajax-submit?
        if(!$field.attr('data-ajax-save') && !$field.attr('data-ajax-validate')) return;

        var $form = getFormFromField($field);
        var $ui = $form.closest('.ui_Form');
        var $saveBadge = $field.find('.field-saveBadge').first(); // First is specified, just in case this is a repeater with fields inside it
        var $spinner = "<i class='fa fa-spin fa-circle-o-notch'></i>";
        var $checkmark = "<i class='fa fa-check-circle'></i>";
        var saveBadgeTimeout = null;
        var timestamp = new Date().getTime();
        var value = params && params.value ? params.value : null;
        var fieldName = $field.attr('data-field-name');
        var $errorElement = $field.find('.field-error').last();
        var $messageElement = $field.find('.field-message').last();

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
                        $fieldUi.trigger('ui-validated', [value]);
                        $fieldUi.trigger('ui-saved', [value]);

                        // Reset changed class
                        if($form.attr('data-track-unsaved-changes')) {
                            $field.removeClass('field_changed');
                        }

                        console.log('Save successful.');

                        $saveBadge.html($checkmark);
                        $errorElement.fadeOut();
                        $messageElement.fadeOut();

                        saveBadgeTimeout = setTimeout(function () {
                            $saveBadge.fadeOut(1000);
                        }, 3000);
                    }
                    else if (data.error) {
                        console.log('Invalid input.');

                        $saveBadge.fadeOut();
                        $errorElement.find('.field-errorTxt').html(data.error);
                        $errorElement.fadeIn();

                        $fieldUi.trigger('ui-error', [data.error]); // Allow other js to pick up on the error event
                    }
                    else { // Value was validated (but not saved)
                        $fieldUi.trigger('ui-validated', [value]);

                        $errorElement.fadeOut();
                        $saveBadge.fadeOut();
                    }

                    if (data.message) {
                        $messageElement.find('.field-messageTxt').html(data.message);
                        $messageElement.fadeIn()

                        $fieldUi.trigger('ui-message', [data.message]); // Allow other js to pick up on the message event
                    } else {
                        $messageElement.fadeOut();
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

    /**
     * When a field's value changes, add the changed class
     */
    $('body').on('ui-value-changed', '.ui', function(e, params) {
        var $form = getFormFromField($(this));
        if($form.attr('data-track-unsaved-changes')) {
            $(this).find('.field').addClass('field_changed');
            if($form.attr('data-warn-unsaved-changes')) {
                window.onbeforeunload = function () { return true; }; // Alert the user if they try to navigate away with unsaved changes
            }
        }
    });

    $('body').on('submit', 'form', function() {
        if($(this).attr('data-warn-unsaved-changes')) {
            window.onbeforeunload = null; // Clear unsaved changes alert
        }
    });

});