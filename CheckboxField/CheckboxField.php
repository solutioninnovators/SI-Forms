<div class="field checkboxField" <?= $ajaxSave ? 'data-ajax-save="1"' : '' ?>>
	<?php if($description): ?>
		<p class="field-description"><?= $description ?></p>
	<?php endif ?>

	<input type="hidden" class="field-fallback" name="<?= $name ?>" value="0" <?= $disabled ? 'disabled' : '' ?> />

	<label class="checkboxField-label <?= $toggleSwitch ? 'toggleSwitch' : '' ?>">
		<input type="checkbox" name="<?= $name ?>" value="1" <?= $value == 1 ? 'checked="checked"' : '' ?> />
		<?php if($toggleSwitch): ?>
			<span class="toggleSwitch-slider"></span>
		<?php endif ?>
		<?= $label ?>
	</label>

	<?php if($notes): ?>
		<p class="field-notes"><?= $notes ?></p>
	<?php endif ?>
</div>