<?php 
namespace Magic\Engine\Sanitizer;
class EmailSanitizer extends AbstractSanitizer {
	
	public function sanitize($email){
		return filter_var($email,FILTER_SANITIZE_EMAIL);
	}

}
?>