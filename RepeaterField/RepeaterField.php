<div class="repeaterField field">

	<label class="repeaterField-label field-label">
		<i class="field-icon fa fa-<?= $icon ?>"></i> <?= $label ?>
		<?php if($required): ?><span class="field-required">*</span><?php endif ?>
	</label>
	
	<?php if($description): ?>
		<p class="field-description"><?= $description ?></p>
	<?php endif ?>

	<ul class="repeaterField-items">
		<?php $i = 0 ?>
		<?php foreach($repeaterItemsOut as $repeaterItem): ?>
			<li class="repeaterField-item <?= $i == 0 ? 'repeaterField-template' : '' ?>">
				<div class="repeaterField-field gGrid">
					<?= $repeaterItem ?>
				</div>
				<?php if(!$readOnly): ?>
					<div class="repeaterField-remove">
						<button type="button" class="btn btn_sm"><i class="fa fa-trash"></i></button>
					</div>
				<?php endif ?>
			</li>
			<?php $i++ ?>
		<?php endforeach ?>
	</ul>

	<?php if(!$readOnly): ?>
		<button type="button" class="repeaterField-addNew btn btn_sm"><i class="fa fa-fw fa-plus"></i> Add New</button>
	<?php endif ?>

	<?php if($notes): ?>
		<p class="field-notes"><?= $notes ?></p>
	<?php endif ?>

	<?php if($error): ?>
		<span class="field-error"><i class="fa fa-caret-up"></i> <?= $error ?></span>
	<?php endif ?>

</div>