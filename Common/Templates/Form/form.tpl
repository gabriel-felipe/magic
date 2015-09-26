<form <?php echo $this->form->getParsedAttrs() ?>>
	<?php foreach ($this->form->getElements() as $element): ?>
		<?php if (!$this->form->inDisplayGroup($element->getName())): ?>
			<?php echo $element->render(); ?>	
		<?php endif ?>
	<?php endforeach ?>
	<?php foreach ($this->form->getDisplayGroups() as $dg) {
		echo $dg->render();
	} ?>
</form>