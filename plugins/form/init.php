<?php 
require_once(path_root."/plugins/form/Form.php");
require_once(path_root."/plugins/form/IFormElement.php");
require_once(path_root."/plugins/form/IFormValidator.php");
require_once(path_root."/plugins/form/IFormSanitizer.php");

/*** class Loader ***/
function ValidatorLoader($class)
{
	
	$path = path_root."/plugins/form/validators/";
	if(strpos($class, "Validator") !== false){		
		$class = str_replace("Validator", "", $class);
		if (!file_exists($path.$class.".php")) {
			return false;
		}
		include ($path.$class.".php");
	}
}
spl_autoload_register('ValidatorLoader');
function ElementLoader($class)
{
	$path = path_root."/plugins/form/elements/";
	if(strpos($class, "Element") !== false){		
		$class = str_replace("Element", "", $class);
		if (!file_exists($path.$class.".php")) {
			return false;
		}
		include ($path.$class.".php");
	}
}
spl_autoload_register('ElementLoader');
function SanitizerLoader($class)
{
	$path = path_root."/plugins/form/sanitizers/";
	if(strpos($class, "Sanitizer") !== false){		
		$class = str_replace("Sanitizer", "", $class);
		if (!file_exists($path.$class.".php")) {
			return false;
		}
		include ($path.$class.".php");
	}
}
spl_autoload_register('SanitizerLoader');
?>