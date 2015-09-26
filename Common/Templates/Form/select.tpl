<select name="<?php echo $this->element->getName(); ?>" <?php echo $this->element->getParsedAttrs() ?>> 
	<?php foreach ($this->element->getOptions() as $value => $alias): ?>
		<option value="<?php echo $value ?>" <?php echo ($this->element->checkValue($value)) ? "selected='selected'" : "" ?>><?php echo $alias ?></option>
	<?php endforeach ?>
</select>