<div class="field selectField" <?= $ajaxSave ? 'data-ajax-save="1"' : '' ?>>

	<label class="field-label" for="<?= $id ?>">
		<?php if($icon): ?><i class="field-icon fa fa-<?= $icon ?>"></i><?php endif ?>
		<?= $label ?>
		<?php if($required): ?><span class="field-required">*</span><?php endif ?>
	</label>

	<div class="field-saveBadge saveBadge"></div>

	<?php if($description): ?>
		<p class="field-description"><?= $description ?></p>
	<?php endif ?>

	<?php if($readOnly): ?>
		<div class="field-readOnly">
			<?php if($selectedLabel): ?>
				<?= $selectedLabel ?>
			<?php else:	?>
				<div class="field-noValue"><i class="fa fa-minus-circle"></i> Not Selected</div>
			<?php endif ?>
		</div>
	<?php else: ?>
		<select id="<?= $id ?>" name="<?= $name ?>" class="field-input txtBox txtBox_select <?= $autocomplete ? 'selectField-autocomplete' : '' ?>" <?= $disabled ? 'disabled' : '' ?>>
			<?php if($placeholder): ?>
				<option value=""><?= $placeholder ?></option>
			<?php endif ?>

			<?php foreach($options as $option): ?>
				<option value="<?= $sanitizer->entities($option['value']) ?>" <?= $value == $option['value'] ? 'selected="selected"' : '' ?>><?= $option['label'] ?></option>
			<?php endforeach ?>
		</select>
	<?php endif ?>

	<?php if($notes): ?>
		<div class="field-notes"><?= $notes ?></div>
	<?php endif ?>

	<?php if($error): ?>
		<span class="field-error"><i class="fa fa-caret-up"></i> <?= $error ?></span>
	<?php endif ?>

</div>