<input <?= $formAttribute ?>
	type="hidden"
	class="<?= $sanitizer->entities1($inputClasses) ?>"
	name="<?= $sanitizer->entities1($name) ?>"
	value="<?= $sanitizer->entities1($value) ?>"
	<?= $disabled ? 'disabled' : '' ?>
/>