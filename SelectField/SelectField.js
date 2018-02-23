/**
 * @todo: prevent chosen call if not using autocomplete. Actually, don't load this file at all in that case.
 */
$(function() {
    function init() {
        $('.selectField-autocomplete').chosen({
            width: "100%"
        });
    }

    init();

    // re-initialize on reload
	$('body').on('reloaded', '.ui_SelectField', function(e) {
		init();
	});
});