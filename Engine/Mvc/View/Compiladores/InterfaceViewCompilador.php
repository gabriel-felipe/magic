<?php 
namespace Magic\Engine\Mvc\View\Compiladores;
use Magic\Engine\Compilador\InterfaceCompilador;
use Magic\Engine\Mvc\View\AbstractView;
interface InterfaceViewCompilador extends InterfaceCompilador{
	function setView(AbstractView $view);
	function getView();
}
?>