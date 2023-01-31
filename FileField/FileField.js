$(function() {
    $('body').on('change', '.fileField input', function() {
        var $this = $(this);
        var $ui = $this.closest('.ui');
        $ui.trigger('ui-value-changed', [{value: $this.val()}]);
    });

    $('.fileListItem-delete').on('click', function() {
        let $this = $(this);
        let ui = $this.closest('.ui');
        let fileNum = $this.attr('filenum');
        let fileName = ui.find(".fileListItem-label[filenum='" + fileNum + "']").text();
        let parent = $this.closest('.fileListItem');
        parent.remove();
        UiBlocks.ajax(ui, 'removeFileFromPage', {fileName: fileName})
    });

    $('body').on('ui-saved', '.ui_FileField', function() {
        console.log("Is this thing on?");
        UiBlocks.reload($(this));
    });
});