<?php 
class EmailSanitizer implements IFormSanitizer
{

	function clean($value){
		return sanitize::email($value);
	}
}
?>