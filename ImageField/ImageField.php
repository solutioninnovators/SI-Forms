<?php include('../Field/header.php') ?>
<div class="field-input imageField-input">
    <input type="file"
        class="<?= $sanitizer->entities1($buttonClasses) ?>"
        name="<?= $sanitizer->entities1($name) ?><?= $multiple ? '[]' : '' ?>"
        <?= $multiple ? "multiple" : "" ?>
        <?php if($allowedExt): ?> accept="<?= implode(",", $allowedExt) ?>"<?php endif ?>
        <?php if($id): ?> id="input_<?= $sanitizer->entities1($id) ?>"<?php endif ?>
        <?= $disabled ? 'disabled' : '' ?>
    />
</div>
<?php include('../Field/footer.php') ?>
<?php if($imgPreview && $savePage->{$saveField}): ?>
    <div class="imageList">
        <?php $multiple ? $d = 100 : $d = 200; ?>
        <?php if(get_class($savePage->{$saveField}) === "ProcessWire\Pageimage"): ?>
            <div class="imageListItem">
                <img class="imageListItem-preview" src="<?= $savePage->{$saveField}->size($d, $d)->url ?>" />
                <button type="button" class="imageListItem-delete btn btn_sm tooltip" name="<?= $savePage->{$saveField}->name ?>" title="Delete File">
                    <i class="fa fa-trash-can fa-fw"></i>
                </button>
            </div>
        <?php else: ?>
            <?php foreach($savePage->{$saveField} as $image): ?>
                <div class="imageListItem">
                    <img class="imageListItem-preview" src="<?= $image->size($d,$d)->url ?>" />
                    <button type="button" class="imageListItem-delete btn btn_sm tooltip" name="<?= $image->name ?>" title="Delete File">
                        <i class="fa fa-trash-can fa-fw"></i>
                    </button>
                </div>
            <?php endforeach ?>
        <?php endif ?>
    </div>
<?php else: ?>
    <div class="field-noValue"><i class="fa fa-minus-circle"></i> No Image</div>
<?php endif ?>
