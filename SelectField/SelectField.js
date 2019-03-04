$(function() {
    function init() {
        var autocompletes = $('.selectField-autocomplete');

        if(autocompletes.length > 0) {
            $('.selectField-autocomplete').chosen({
                width: "100%"
            });
        }
    }

    init();

    // re-initialize on reload
    $('body').on('reloaded', '*', function(e) {
        init();
    });
});