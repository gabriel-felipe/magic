<?php 
namespace Magic\Engine\Adapter\ColumnToElement;
use Magic\Engine\Form\Elements\TextElement;
use Magic\Engine\Datamgr\DbColumn;
class SimpleInput implements InterfaceColumnToElement
{
	function match(DbColumn $column)
	{
		return true;
	}
	function getElement(DbColumn $column){
		$element = new TextElement($column->getName());
		if (!$column->getNull()) {
			$element->setRequired(true);
		}
		return $element;
	}
}
?>