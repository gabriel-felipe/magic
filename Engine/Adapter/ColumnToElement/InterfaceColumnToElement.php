<?php 
namespace Magic\Engine\Adapter\ColumnToElement;
use Magic\Engine\Datamgr\DbColumn;
interface InterfaceColumnToElement {
	public function match(DbColumn $column);
	public function getElement(DbColumn $column);
}
?>
