<?php 
namespace Magic\Common\Adapter\ColumnToElement;
use Magic\Engine\Adapter\ColumnToElement\InterfaceColumnToElement;
use Magic\Engine\Form\Elements\SelectElement;
use Magic\Engine\Datamgr\DbColumn;
use Magic\Engine\Datamgr\DbModel;
use Magic\Engine\Datamgr\DbModelPlural;
use Magic\Engine\Datamgr\Driver\DbDriverFactory;
class FkToSelect implements InterfaceColumnToElement
{	
	protected $aliasColumns = array();
	function __construct($aliasColumns = array("nome","titulo","alias")){
		$this->aliasColumns = $aliasColumns;
	}	

	function match(DbColumn $column)
	{	
		$reference = $column->getReferences();
		if ($reference) {
			return true;
		}
		return false;
	}
	function getElement(DbColumn $column){
		$reference = explode(".",$column->getReferences());

		$element = new SelectElement($column->getName());
		if (!$column->getNull()) {
			$element->setRequired(true);
		}
		$refTable = $reference[0];
		$refColumn = $reference[1];
		$aliasColumn = $column->getName();
		$dbManager = DbDriverFactory::getDbManager();
		$refTableColumns = array_map(function($v){return $v->getName();},$dbManager->fetchColumns($refTable));
		foreach ($this->aliasColumns as $column) {
			if (in_array($column, $refTableColumns)) {
				$aliasColumn = $column;
				break;
			}
		}
		$columns = array($refColumn);
		if ($aliasColumn !== $refColumn) {
			$columns[] = $aliasColumn;
		}
		$dbModel = new DbModel($dbManager,$refTable,$refColumn,$columns);
		$DbModelPlural = new DbModelPlural($dbModel);
		$DbModelPlural->all();
		$options = array();
		foreach($DbModelPlural->info() as $obj){
			$key = $obj[$refColumn];
			$options[$key] = $obj[$aliasColumn];
		}
		$element->setOptions($options);
		return $element;
	}
}
?>