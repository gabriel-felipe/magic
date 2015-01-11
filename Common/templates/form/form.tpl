<form <?php echo $this->form->getParsedAttrs() ?>>
	<?php foreach ($this->form->getElements() as $element): ?>
		
		<?php echo $element->render(); ?>
	<?php endforeach ?>
</form>