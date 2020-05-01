/**
 * @todo: prevent chosen call if not using autocomplete.
 */
$(function() {
    function init() {
        $('.selectField-autocomplete').chosen({
            width: "100%"
        });
    }

    init();

    // re-initialize on reload
	$('body').on('reloaded', '*', function(e) {
		init();
	});
});