$(function() {
   $('.passwordField-visibility').on('click', function() {
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


    $('body').on('input', '.passwordField .field-input', debounce(function(e) {
        console.log('Field changed.');

        var $field = $(this).closest('.ui');
        var $inputs = $field.find('.field-input');

        var valArr = [];
        $inputs.each(function() {
            valArr.push(this.value);
        });

        $field.trigger('valueChange', [{value: valArr}]);

    }, 500));
});