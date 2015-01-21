<?php 
use Magic\Scopes\wireframes\model\Pages;
class ControllerLayoutSidebar extends ScopeController
{
	
	function index()
	{
		$pages = new Pages;
		$this->view->pages = $pages->getPages();
		echo $this->getContent();
	}

}
?>