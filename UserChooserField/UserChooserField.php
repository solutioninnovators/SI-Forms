<?php include('../Field/header.php') ?>

	<?php if($readOnly): ?>

		<ul class="userChooser">
			<?php
			foreach($options as $option):
				if(in_array($option->id, $value)):
			?>
				<li class="userChooser-item userChooser-readOnly userChooser-selected">
					<img class="userChooser-img tooltip" src="<?= $option->user_img->size($thumbnailSize,$thumbnailSize)->url ?>" title="<?= $sanitizer->entities1($option->fullName()) ?>" alt="<?= $sanitizer->entities1($option->initials) ?>" />

					<?php if($showInitials): ?>
						<div class="userChooser-label"><?= $sanitizer->entities1($option->initials) ?></div>
					<?php endif ?>
				</li>
			<?php
				endif;
			endforeach;
			?>
		</ul>

	<?php else: ?>
		<input type="hidden" <?= $formAttribute ?> class="field-fallback" name="<?= $sanitizer->entities1($name) ?>" value="[#]" <?= $disabled ? 'disabled' : '' ?> />
	
		<ul class="userChooser <?= $singular ? 'userChooser_singular' : '' ?> <?= $useDropDown ? 'userChooser_useDropDown' : '' ?>">
			<?php if($useDropDown): ?>
				<button type="button" class="userChooser-placeholder tooltip" title="Choose User" style="width:<?= $thumbnailSize ?>px; height:<?= $thumbnailSize ?>px; <?= count($value) ? 'display: none;' : '' ?>">
					<i class="fa fa-user-plus"></i>
				</button>
			<?php endif ?>

			<?php foreach($options as $option): ?>
				<li class="userChooser-item <?= in_array($option->id, $value) ? 'userChooser-selected' : '' ?>">
					<img class="userChooser-img tooltip" src="<?= $option->user_img->size($thumbnailSize,$thumbnailSize)->url ?>" title="<?= $sanitizer->entities1($option->fullName()) ?>" alt="<?= $sanitizer->entities1($option->initials) ?>" />

					<?php if($showInitials): ?>
						<div class="userChooser-label"><?= $sanitizer->entities1($option->initials) ?></div>
					<?php endif ?>

					<input <?= $formAttribute ?> class="userChooser-checkbox field-input" type="checkbox" name="<?= $sanitizer->entities1($name) ?>[]" value="<?= $sanitizer->entities1($option->id) ?>" <?= $value && in_array($option->id, $value) ? 'checked="checked"' : '' ?> <?= $disabled ? 'disabled' : '' ?> />
				</li>
			<?php endforeach ?>
		</ul>

	<?php endif ?>

<?php include('../Field/footer.php') ?>