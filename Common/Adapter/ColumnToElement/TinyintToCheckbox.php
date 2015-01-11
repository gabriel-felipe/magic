<?php 
namespace Magic\Common\Adapter\ColumnToElement;
use Magic\Engine\Adapter\ColumnToElement\InterfaceColumnToElement;
use Magic\Engine\Form\Elements\CheckboxElement;
use Magic\Engine\Validator\StringValidator;
use Magic\Engine\Datamgr\DbColumn;
class TinyintToCheckbox implements InterfaceColumnToElement
{
	function match(DbColumn $column)
	{	
		preg_match("/.*\(([^)]+)\).*/", $column->getType(),$matches);
		$tipo = $column->getType();
		$size = (isset($matches[1])) ? $matches[1] : false;
		if (strpos($tipo, "int") !== false and (int)$size === 1) {
			return true;
		} elseif(strpos($tipo, "tinyint") !== false) {
			return true;
		}
		return false;
	}
	function getElement(DbColumn $column){
		$element = new CheckboxElement($column->getName());
		if (!$column->getNull()) {
			$element->setRequired(true);
		}		
		return $element;
	}
}
?>