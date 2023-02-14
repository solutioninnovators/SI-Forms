<?php include('../Field/header.php') ?>

	<input <?= $formAttribute ?>type="hidden" class="field-fallback" name="<?= $sanitizer->entities1($name) ?>" value="[#]" <?= $disabled ? 'disabled' : '' ?> />

    <?php if($readOnly): ?>
        <div class="field-readOnly">
            <?php if($selectedLabel): ?>
                <?= $sanitizer->entities1($selectedLabel) ?>
            <?php else:	?>
                <div class="field-noValue"><i class="fa fa-minus-circle"></i> Not Selected</div>
            <?php endif ?>
        </div>
    <?php else: ?>

        <select <?= $formAttribute ?> class="field-input multipleSelectField-select" multiple="multiple" name="<?= $sanitizer->entities1($name) ?>[]" <?php if($id): ?>id="input_<?= $sanitizer->entities1($id) ?>"<?php endif ?> data-settings="<?= $sanitizer->entities1(json_encode($settings)) ?>" <?= $disabled ? 'disabled' : '' ?>>
            <?php foreach($options as $option): ?>
                <option value="<?= $sanitizer->entities1($option['value']) ?>" <?= in_array($option['value'], $value) ? 'selected="selected"' : '' ?>><?= $sanitizer->entities1($option['label']) ?></option>
            <?php endforeach ?>
        </select>
    <?php endif ?>

<?php include('../Field/footer.php') ?>