<?php 
interface InterfaceViewCompilador extends InterfaceCompilador{
	function setView(AbstractView $view);
	function getView();
}
?>