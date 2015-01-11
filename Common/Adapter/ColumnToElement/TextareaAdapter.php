<?php 
namespace Magic\Common\Adapter\ColumnToElement;
use Magic\Engine\Adapter\ColumnToElement\InterfaceColumnToElement;
use Magic\Engine\Form\Elements\TextareaElement;
use Magic\Engine\Validator\StringValidator;
use Magic\Engine\Datamgr\DbColumn;
class TextareaAdapter implements InterfaceColumnToElement
{
	function match(DbColumn $column)
	{	
		preg_match("/.*\(([^)]+)\).*/", $column->getType(),$matches);
		$tipo = $column->getType();
		$size = (isset($matches[1])) ? $matches[1] : false;
		if (strpos($tipo, "varchar") !== false and $size > 400) {
			return true;
		} elseif(strpos($tipo, "text") !== false) {
			return true;
		}
		return false;
	}
	function getElement(DbColumn $column){
		$element = new TextareaElement($column->getName());
		if (!$column->getNull()) {
			$element->setRequired(true);
		}
		preg_match("/.*\(([^)]+)\).*/", $column->getType(),$matches);
		$tipo = $column->getType();
		$size = (isset($matches[1])) ? $matches[1] : false;
		if (strpos($tipo, "varchar") !== false) {
			$validator = new StringValidator(1,$size);
			$element->addValidator($validator);
		} elseif(strpos($tipo, "text") !== false) {
			$validator = new StringValidator(1,false);
			$element->addValidator($validator);
		}
		return $element;
	}
}
?>