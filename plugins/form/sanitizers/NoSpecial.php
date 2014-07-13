<?php 
class NoSpecialSanitizer implements IFormSanitizer
{

	function clean($value){
		return sanitize::no_special($value);
	}
}
?>