<?php include('../Field/header.php') ?>
	<?php if(!$readOnly): ?>
		<input <?= $formAttribute ?> type="hidden" class="field-fallback" name="<?= $sanitizer->entities1($name) ?>" value="[#]" />
	<?php endif ?>

	<ul class="repeaterField-items" data-item-limit="<?= $sanitizer->entities1($itemLimit) ?>" <?= $sortable ? 'data-sortable="1"' : '' ?>>
		<?php $i = 0 ?>
		<?php foreach($repeaterItemsOut as $repeaterItem): ?>
			<li class="repeaterField-item <?= $i == 0 ? 'repeaterField-template' : '' ?> <?= $sortable && !$readOnly ? 'repeaterField-item_sortable grabbable' : '' ?>">
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
		<button type="button" class="repeaterField-addNew btn btn_sm <?= $itemLimit && count($repeaterItemsOut) >= $itemLimit ? 'hide' : '' ?>" ><i class="fa fa-fw fa-plus"></i> Add New</button>
	<?php endif ?>

<?php include('../Field/footer.php') ?>