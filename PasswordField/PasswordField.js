$(function() {
   $('body').on('click', '.passwordField-visibility', function() {
       var $this = $(this);
       var $inputs = $(this).closest('.field').find('.field-input');
       var $input = $inputs.first();
       var $icon = $this.find('i');

       if($input.attr('type') == 'password') {
           $inputs.attr('type', 'text');
           $icon.removeClass('fa-eye').addClass('fa-eye-slash');
       }
       else {
           $inputs.attr('type', 'password');
           $icon.removeClass('fa-eye-slash').addClass('fa-eye');
       }

       $input.focus();
   });


    $('body').on('input', '.passwordField .field-input', UiBlocks.debounce(function(e) {
        var $field = $(this).closest('.ui');
        var $inputs = $field.find('.field-input');

        var valArr = [];
        $inputs.each(function() {
            valArr.push(this.value);
        });

        $field.trigger('ui-value-changed', [{value: valArr}]);

    }, 500));
});