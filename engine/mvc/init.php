<?php 
require_once(path_engine."/mvc/view/compiladores/InterfaceViewCompilador.php");
require_once(path_engine."/mvc/view/compiladores/AbstractViewCompiladorDecorator.php");
require_once(path_engine."/mvc/view/compiladores/ViewCompilador.php");
require_once(path_engine."/mvc/view/ViewHandler.php");
require_once(path_engine."/mvc/view/AbstractView.php");
require_once(path_engine."/mvc/url.php");
require_once(path_engine."/mvc/action.php");
require_once(path_engine."/mvc/controller.php");

$ViewHandler = new ViewHandler($registry);
$registry->set("ViewHandler",$ViewHandler);
?>