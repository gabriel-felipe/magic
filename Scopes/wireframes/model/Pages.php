<?php 
namespace Magic\Scopes\wireframes\model;
class Pages
{
	function getPages()
	{
		global $registry;
		$scope = $registry->get("scope");
		$files = glob($scope->getTemplateFolder()."/pages/*.tpl");
		$pages = array();
		foreach ($files as $file) {
			$pages[$file] = str_replace(".tpl","",basename($file));
		}
		return $pages;
	}
}
?>