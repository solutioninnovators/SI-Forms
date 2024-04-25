$(function() {
    $('body').on('change', '.fileField input', function() {
        var $this = $(this);
        var $ui = $this.closest('.ui');
        $ui.trigger('ui-value-changed', [{value: $this.val()}]);
    });

    $('body').on('click', '.imageListItem-delete', function () {
        if(confirm("Are you sure that you want to delete this image?")) {
            let $this = $(this);
            let ui = $this.closest('.ui');
            let fileName = $this.attr('name');
            let parent = $this.closest('.imageListItem');
            parent.remove();
            UiBlocks.ajax(ui, 'removeFileFromPage', {fileName: fileName})
        }
    });

    $('body').on('ui-saved', '.ui_FileField', function() {
        UiBlocks.reload($(this));
    });
});