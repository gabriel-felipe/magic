<sidebar id='page-list'>
	<?php foreach ($this->pages as $file => $name): ?>
		<article class='page' data-page="<?php echo $name ?>">
			<?php echo $name ?>
		</article>
	<?php endforeach ?>
</sidebar>