<?php 
class SpecialCharsSanitizer implements IFormSanitizer
{
	function clean($value){
		return sanitize::special_chars($value);
	}
}
?>