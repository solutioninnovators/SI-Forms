$(function() {
    function updateIndexes($repeater) {
		var items = $repeater.find('.repeaterField-item:not(.repeaterField-template)');
		var i = 0;
		items.each(function() {
			$(this).find('.field-input').each(function() {
                var $this = $(this);
				if($this.attr("name")) {
					var newName = $this.attr("name").replace(/\[(\d+|\$)\]/, '[' + i + ']');
					$this.attr('name', newName);
				}
			});
			i++;
		});
	}

    $('body').on('click', '.repeaterField-addNew', function(e) {
        e.preventDefault();
        var $repeater = $(this).closest('.repeaterField');

        // Get a copy of the hidden template element
        var $newItem = $repeater.find('.repeaterField-template').clone();
        $newItem.find('.field-input').removeAttr('disabled');
        $newItem.removeClass('repeaterField-template').hide();
        $repeater.find('.repeaterField-items').append($newItem);
        $newItem.slideDown(100);

        updateIndexes($repeater)
    });

    $('body').on('click', '.repeaterField-remove', function(e) {
        e.preventDefault();
        var $repeater = $(this).closest('.repeaterField');

        var $item = $(this).closest('.repeaterField-item');
        $item.slideUp(100);
        setTimeout(function() {
            $item.remove();
            updateIndexes($repeater);
        }, 100);
    });
});