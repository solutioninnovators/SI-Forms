<?php include('../Field/header.php') ?>

	<?php if($readOnly): ?>

		<ul class="userChooser group">
			<?php
			foreach($options as $option):
				if(in_array($option->id, $value)):
			?>
				<li class="userChooser-item userChooser-readOnly userChooser-selected left">
					<img class="userChooser-img tooltip" src="<?= $option->user_img->size(100,100)->url ?>" title="<?= $sanitizer->entities1($option->fullName()) ?>" alt="<?= $sanitizer->entities1($option->initials) ?>" />
					<div class="userChooser-label"><?= $sanitizer->entities1($option->initials) ?></div>
				</li>
			<?php
				endif;
			endforeach;
			?>
		</ul>

	<?php else: ?>
		<input type="hidden" class="field-fallback" name="<?= $sanitizer->entities1($name) ?>" value="[#]" />
	
		<ul class="userChooser <?= $singular ? 'userChooser_singular' : '' ?> group">
			<?php foreach($options as $option): ?>
				<li class="userChooser-item left <?= in_array($option->id, $value) ? 'userChooser-selected' : '' ?>">

					<img class="userChooser-img tooltip" src="<?= $option->user_img->size(100,100)->url ?>" title="<?= $sanitizer->entities1($option->fullName()) ?>" alt="<?= $sanitizer->entities1($option->initials) ?>" />
					<div class="userChooser-label"><?= $sanitizer->entities1($option->initials) ?></div>

					<input class="userChooser-checkbox" type="checkbox" name="<?= $sanitizer->entities1($name) ?>[]" value="<?= $sanitizer->entities1($option->id) ?>" <?= $value && in_array($option->id, $value) ? 'checked="checked"' : '' ?> />

				</li>
			<?php endforeach ?>
		</ul>

	<?php endif ?>

<?php include('../Field/footer.php') ?>