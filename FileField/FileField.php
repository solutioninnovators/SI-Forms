<?php include('../Field/header.php') ?>

<div class="field-input fileField-input">
    <input type="file"
        class="<?= $sanitizer->entities1($buttonClasses) ?>"
        name="<?= $sanitizer->entities1($name) ?><?= $multiple ? '[]' : '' ?>"
        <?= $multiple ? "multiple" : "" ?>
        <?php if($allowedExt): ?> accept="<?= implode(",", $allowedExt) ?>"<?php endif ?>
        <?php if($id): ?> id="input_<?= $sanitizer->entities1($id) ?>" <?php endif ?>
        <?= $disabled ? 'disabled' : '' ?>
    />
</div>
<?php include('../Field/footer.php') ?>
<?php if($displayValue): ?>
    <?php $x = 1; foreach($displayValue as $file): ?>
        <div class="fileListItem" filenum="<?= $x ?>">
            <div class="fileListItem-left">
                <form method='post' action='' >
                    <a class="btn btn_sm tooltip fileListItem-btn fileListItem-download"
                       href="<?= $this->wire->urls->files . $savePage->id . "/" . $file->name ?>"
                       target="_blank"
                       filenum="<?= $x ?>"
                       title="Download">
                        <i class="fa fa-download fa-fw"></i>
                    </a>
                </form>
                <span class="fileListItem-label tooltip" title="<?= $file->name ?>" filenum="<?= $x ?>"><?= $file->name ?></span>
            </div>
            <div class="fileListItem-right">
                <button type="button" class="btn btn_sm tooltip fileListItem-btn fileListItem-delete" filenum="<?= $x ?>" title="Delete File">
                    <i class="fa fa-trash-can fa-fw"></i>
                </button>
            </div>
        </div>
        <?php $x = $x + 1; endforeach ?>
<?php endif ?>
