<?php 
namespace Magic\Engine\Adapter\ColumnToElement;
use Magic\Engine\Form\Elements\TextElement;
use Magic\Engine\Validator as V;
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
		preg_match("/.*\(([^)]+)\).*/", $column->getType(),$matches);
		$tipo = $column->getType();
		$size = (isset($matches[1])) ? $matches[1] : false;
		if (strpos($tipo, "varchar") !== false) {
			$validator = new V\StringValidator(1,$size);
			$element->addValidator($validator);
		}
		return $element;
	}
}
?>