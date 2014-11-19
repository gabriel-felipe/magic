<?php 
$this->load->plugin("form");
$this->html->add_common_js("common.functions.js",1,1);
$this->html->add_common_js("crud.js",1,1);
function CrudLoader($class)
{
	$path = path_root."/plugins/crud/";
	if (!file_exists($path.$class.".php")) {
		return false;
	}
	include ($path.$class.".php");
}
spl_autoload_register('CrudLoader');
?>