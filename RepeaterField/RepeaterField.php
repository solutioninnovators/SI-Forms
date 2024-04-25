<?php include('../Field/header.php') ?>
	<input <?= $formAttribute ?> type="hidden" class="field-fallback" name="<?= $sanitizer->entities1($name) ?>" value="[#]" <?= $disabled ? 'disabled' : '' ?> />

	<ul class="repeaterField-items" data-item-limit="<?= $sanitizer->entities1($itemLimit) ?>" data-sortable="<?= $sortable ?>" data-read-only="<?= $readOnly ?>" data-show-sort-handle="<?= $showSortHandle ?>" data-sortable-handle=".<?= $sortHandleClass ?>" data-show-label-once="<?= $showLabelOnce ?>">
		<?php $i = 0 ?>
		<?php foreach($repeaterItemsOut as $repeaterItem): ?>
			<li class="repeaterField-item <?= $i == 0 ? 'repeaterField-template' : '' ?> <?= $sortable && !$readOnly && !$showSortHandle ? "grabbable" : '' ?>">
				<?php if($showSortHandle): ?>
					<div class="<?= $sortHandleClass ?> grabbable"><i class="fa fa-grip-lines"></i></div>
				<?php endif; ?>
				<div class="repeaterField-field <?= $innerClasses ?>">
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
		<button type="button" class="repeaterField-addNew btn btn_sm <?= $itemLimit && count($repeaterItemsOut) >= $itemLimit ? 'hide' : '' ?>" ><?= $addNewIcon ?> Add New</button>
	<?php endif ?>

<?php include('../Field/footer.php') ?>