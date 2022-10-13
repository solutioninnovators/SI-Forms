$(function() {
	$('body').on('change', '.radioTab input', function() {
		var $ui = $(this).closest('.ui');
		$ui.trigger('ui-value-changed', [{value: $ui.find('input:checked').val()}]);
	});
});