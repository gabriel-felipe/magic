<?php 
class SlugSanitizer implements IFormSanitizer
{

	function clean($value){
		return strtolower(preg_replace("/[^A-Za-z0-9-]+/","-",$value));
	}
}
?>