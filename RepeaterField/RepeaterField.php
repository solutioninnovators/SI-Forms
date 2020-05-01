<?php include('../Field/header.php') ?>

	<label class="repeaterField-label field-label">
		<i class="field-icon fa fa-<?= $icon ?>"></i> <?= $label ?>
		<?php if($required): ?><span class="field-required">*</span><?php endif ?>
	</label>
	
	<?php if($description): ?>
		<p class="field-description"><?= $description ?></p>
	<?php endif ?>

	<ul class="repeaterField-items" data-item-limit="<?= isset($itemLimit)? $itemLimit : -1 ?>">
		<?php $i = 0 ?>
		<?php foreach($repeaterItemsOut as $repeaterItem): ?>
			<li class="repeaterField-item <?= $i == 0 ? 'repeaterField-template' : '' ?>">
				<div class="repeaterField-field gGrid">
					<?= $repeaterItem ?>
				</div>
				<?php if(!$readOnly): ?>

				<?php endif ?><div class="repeaterField-remove">
                    <button type="button" class="btn btn_sm"><i class="fa fa-trash"></i></button>
                </div>
			</li>
			<?php $i++ ?>
		<?php endforeach ?>
	</ul>

	<?php if(!$readOnly): ?>
		<button type="button" class="repeaterField-addNew btn btn_sm <?= (!isset($itemLimit) || $itemLimit == 1) ? 'hide':'' ?>" ><i class="fa fa-fw fa-plus"></i> Add New</button>
	<?php endif ?>

<?php include('../Field/footer.php') ?>