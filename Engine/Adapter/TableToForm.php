<?php 
namespace Magic\Engine\Adapter;
use Magic\Engine\Datamgr\AbstractDbManager;
use Magic\Engine\Form as Form;
use Magic\Engine\Form\Elements as Element;
use Magic\Engine\Form\Decorators\LabelDecorator;
use Magic\Engine\Form\Decorators\TagWrapperDecorator;
use Magic\Engine\Sanitizer\EmailSanitizer;
use Magic\Engine\Validator\EmailValidator;
use Magic\Engine\Validator\StringValidator;
use Magic\Engine\Adapter\ColumnToElement\InterfaceColumnToElement;
class TableToForm
{
	protected $db = false;
	protected $adapters=array();
	protected $form=null;
	function __construct(AbstractDbManager $db,Form\Form $form=null){
		$this->db = $db;
		if ($form) {
			$this->form = $form;
		}
	}
	function addAdapter(InterfaceColumnToElement $adapter){
		$this->adapters[] = $adapter;
	}
	function getForm($table){
		$elements = $this->getElements($table);
		$form = ($this->form) ? $this->form : new Form\Form("frm".$table);
		foreach ($elements as $el) {
			$form->prepare($el);
			$form->addElement($el);
		}		
		return $form;
	}
	function getColumns($table){
		return $this->db->fetchColumns($table);
	}
	function getElement($column){
		foreach ($this->adapters as $adapter) {
			$match = $adapter->match($column);
			if ($match) {
				return $adapter->getElement($column);
			}
		}
	}
	function getElements($table){
		$columns = $this->getColumns($table);
		$elements = array();
		foreach ($columns as $column) {
			$element = $this->getElement($column);
			if ($element) {
				$elements[$column->getName()] = $element;
			}
			
		}
		return $elements;
	}
}
?>