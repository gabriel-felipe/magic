<?php 
class SlugSanitizer implements IFormSanitizer
{

	function clean($value){
		return sanitize::no_accents_n_spaces($value);
	}
}
?>