<?php 
namespace Magic\Engine\Adapter\ColumnToElement;
use Magic\Engine\Form\Elements\HiddenElement;
use Magic\Engine\Datamgr\DbColumn;
class HiddenPk implements InterfaceColumnToElement
{
	function match(DbColumn $column)
	{
		if ($column->getPrimaryKey() and $column->getAutoIncrement()) {
			return true;
		} else {
			return false;
		}
	}
	function getElement(DbColumn $column){
		return new HiddenElement($column->getName());
	}
}
?>