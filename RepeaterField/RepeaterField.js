$(function() {
    var nameRegEx = /\[(\d+|\$)\]/;
    var idRegEx = /__(\d+|\$)__/;

    init($('.repeaterField'));

    $('body').on('ui-reloaded', '.ui', function(e) {
        e.stopPropagation();
        init($(this).find('.repeaterField'));
    });

    function init($fields) {
        $fields.each(function() {
            var list = $(this).find('.repeaterField-items');
            if(parseInt(list.attr('data-sortable'))) {
                Sortable.create(list[0], {
                    animation: 150, // ms, animation speed moving items when sorting, `0` — without animation
                    //handle: ".repeaterField-sort", // Restricts sort start click/touch to the specified element
                    onUpdate: function (e) {
                        var $item = $(e.item); // the current dragged HTMLElement

                        updateIndexes($item.closest('.repeaterField')); // Update repeater indexes
                        var $repeaterUi = $item.closest('.ui_RepeaterField');
                        $repeaterUi.trigger('ui-resorted'); // Trigger value changed event on the repeater when sort order changes
                        $repeaterUi.trigger('ui-value-changed'); // Trigger value changed event on the repeater when sort order changes
                    }
                });
            }
        });
    }

    function updateIndexes($repeater) {
		var items = $repeater.children('.repeaterField-items').children('.repeaterField-item:not(.repeaterField-template)');
		var i = 0;
		items.each(function() {
            $repeaterItem = $(this);
            $fieldUis = $repeaterItem.children('.repeaterField-field').children('.ui'); // Limiting to direct children to potentially allow support for nested repeaters

            // This section will have to be updated if we want to support nesting repeaters
            $fieldUis.each(function() {
                $fieldUi = $(this);

                // Find the element(s) in this field with the name attribute (input, select, textarea...) and update it to reflect the new index #
                reindexAttributes($fieldUi.find('[name]'), 'name', i, 'name');

                // Also update id attributes for these
                reindexAttributes($fieldUi.find('[id]'), 'id', i, 'id');

                // Update 'for' on the label element
                reindexAttributes($fieldUi.find('[for]'), 'for', i, 'id');

                // Update data-field-name attribute on the .field element
                reindexAttributes($fieldUi.find('[data-field-name]'), 'data-field-name', i, 'name');
            });

            // Update attributes on the Ui wrappers
            reindexAttributes($fieldUis, 'id', i, 'id');
            reindexAttributes($fieldUis, 'data-ui-id', i, 'id');
            // reindexAttributes($fieldUis, 'data-ui-path', i, 'id'); // Updating the path breaks ajax calls on UIs within the newly created items because the item doesn't exist yet on the server. By not updating the path, we route all ajax calls through the template item, which should be sufficient at this time.
            reindexAttributes($fieldUis, 'class', i, 'id');

			i++;
		});
	}

    function reindexAttributes($elements, attr, index, type) {
        var selector = '[' + attr + ']';
        $elements.filter(selector).each(function() {
            var $this = $(this);
            if(type == 'id') {
                var newString = $this.attr(attr).replace(idRegEx, '__' + index + '__');
            }
            else if(type == 'name') {
                var newString = $this.attr(attr).replace(nameRegEx, '[' + index + ']');
            }
            $this.attr(attr, newString);
        });
    }

    $('body').on('click', '.repeaterField-addNew', function(e) {
        e.preventDefault();
        var $repeater = $(this).closest('.repeaterField');

        var itemLimit = $repeater.find('.repeaterField-items').attr('data-item-limit');
        var itemCount = $repeater.find('.repeaterField-field').length;
        if(itemLimit && itemCount >= itemLimit)
            $repeater.find('.repeaterField-addNew').hide();

        // Get a copy of the hidden template element
        var $newItem = $repeater.find('.repeaterField-template').clone();
        $newItem.find('[disabled]').removeAttr('disabled');
        $newItem.removeClass('repeaterField-template').hide();
        $repeater.find('.repeaterField-items').append($newItem);
        $newItem.slideDown(100);

        updateIndexes($repeater);
        $newItem.find('.ui').trigger('ui-reloaded'); // Trigger reloaded event on the fields in the new item so that they can initialize any javascript that supports them

        var $repeaterUi = $newItem.closest('.ui_RepeaterField');
        $repeaterUi.trigger('ui-item-added'); // Trigger value changed event on the repeater when sort order changes
        $repeaterUi.trigger('ui-value-changed'); // Trigger value changed event on the repeater when item is added
    });

    $('body').on('click', '.repeaterField-remove', function(e) {
        e.preventDefault();
        var $repeater = $(this).closest('.repeaterField');

        var itemLimit = $repeater.find('.repeaterField-items').attr('data-item-limit');
        var itemCount = $repeater.find('.repeaterField-field').length - 1;
        if(itemCount <= itemLimit)
            $repeater.find('.repeaterField-addNew').show();

        var $item = $(this).closest('.repeaterField-item');
        $item.slideUp(100);
        setTimeout(function() {
            var $repeaterUi = $item.closest('.ui_RepeaterField');
            $item.remove();
            updateIndexes($repeater);
            $repeaterUi.trigger('ui-item-removed'); // Trigger value changed event on the repeater when sort order changes
            $repeaterUi.trigger('ui-value-changed'); // Trigger value changed event on the repeater when item is deleted
        }, 100);
    });

    // When any field inside the repeater changes, also trigger a change on the repeater itself
    $('body').on('ui-value-changed', '.ui', function() {
        var $repeaterParent = $(this).parent().closest('.ui_RepeaterField');
        if($repeaterParent.length) {
            $repeaterParent.trigger('ui-value-changed');
        }
    });

});