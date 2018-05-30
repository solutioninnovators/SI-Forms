<div class="field <?= $classes ?>">

	<label class="field-label">
		<?php if($icon): ?><i class="field-icon fa fa-<?= $icon ?>"></i><?php endif ?>
		<?= $label ?>
		<?php if($required): ?><span class="field-required">*</span><?php endif ?>
	</label>

	<?php if($description): ?>
		<p class="field-description"><?= $description ?></p>
	<?php endif ?>

	<?php if($readOnly): ?>

		<ul class="userChooser group">
			<?php
			foreach($options as $option):
				if(in_array($option->id, $value)):
			?>
				<li class="userChooser-item userChooser-readOnly userChooser-selected left">
					<img class="userChooser-img tooltip" src="<?= $option->user_img->size(100,100)->url ?>" title="<?= $option->fullName() ?>" alt="<?= $option->initials ?>" />
					<div class="userChooser-label"><?= $option->initials ?></div>
				</li>
			<?php
				endif;
			endforeach;
			?>
		</ul>

	<?php else: ?>
		<input type="hidden" class="field-fallback" name="<?= $name ?>" value="[#]" />
	
		<ul class="userChooser <?= $singular ? 'userChooser_singular' : '' ?> group">
			<?php foreach($options as $option): ?>
				<li class="userChooser-item left <?= in_array($option->id, $value) ? 'userChooser-selected' : '' ?>">

					<img class="userChooser-img tooltip" src="<?= $option->user_img->size(100,100)->url ?>" title="<?= $option->fullName() ?>" alt="<?= $option->initials ?>" />
					<div class="userChooser-label"><?= $option->initials ?></div>

					<input class="userChooser-checkbox" type="checkbox" name="<?= $name ?>[]" value="<?= $option->id ?>" <?= $value && in_array($option->id, $value) ? 'checked="checked"' : '' ?> />

				</li>
			<?php endforeach ?>
		</ul>

	<?php endif ?>

	<?php if($notes): ?>
		<div class="field-notes"><?= $notes ?></div>
	<?php endif ?>

	<?php if($error): ?>
		<span class="field-error"><i class="fa fa-caret-up"></i> <?= $error ?></span>
	<?php endif ?>

</div>