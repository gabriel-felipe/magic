<?php 
namespace Magic\Engine\Helper\Paginator\Adapter;
use \Magic\Engine\Helper\Paginator\AbstractPaginator;
use \Magic\Engine\Datamgr\DbModelPlural;
/**
* 
*/
class DbModelPluralPaginator extends AbstractPaginatorAdapter
{	
	protected $dbModelPlural;
	function init($adapted){
		if (!$adapted instanceof DbModelPlural) {
			throw new \UnexpectedValueException("DbModelPlural Paginator Adapter expects a DbModelPlural object to adapt from.", 1);
			
		}
		$this->dbModelPlural = $adapted;
		$this->dbModelPlural->registerHook(new \Magic\Engine\Datamgr\Hooks\DbModelCallbackHook(array($this,"map")),"afterSelect");
	}
	function map(){
		
		$this->paginator->setShowing(count($this->dbModelPlural->plural));
		$this->paginator->setQtnByPage($this->dbModelPlural->lastSelect->getQtnByPage());
		$totalSelect = clone $this->dbModelPlural->lastSelect;
		$totalSelect->setPage(false);
		$totalSelect->setQtnByPage(false);
		$totalSelect->setFields(array());
		$totalSelect->addCustomField("count(*)","total");
		$result = $totalSelect->run();
		$total = (isset($result[0][0])) ? $result[0][0]['total'] : 0;
		$this->paginator->setTotalItens($total);
		try {
			$this->paginator->setPage($this->dbModelPlural->lastSelect->getPage());
		} catch (\Magic\Engine\Helper\Paginator\PaginatorException $e) {
			try {
				$this->paginator->setPage(1);
			} catch (\Magic\Engine\Helper\Paginator\PaginatorException $e) {
				$this->paginator->setPage(0);
			}
		}
		return $this;
	}
}

?>