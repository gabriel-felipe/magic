<?php 
namespace Magic\Engine\Form;
use Magic\Engine\Form\Decorators\AbstractFormDecorator;
use Magic\Engine\Mvc\View\Compiladores\ViewCompilador;
use Magic\Engine\Mvc\View\Compiladores\InterfaceViewCompilador;
use Magic\Engine\Mvc\View\AbstractView;

/**
* Classe para lidar com forms no backend. Validação / Limpeza de dados.
*/
class Form
{
	protected $elements = array();
	protected $validateErrors = array();
	protected $decorators = array();
	protected $view = false;
	protected $attrs = array();
	protected $id = false;
	protected $displayGroupClass="Magic\Engine\Form\DisplayGroup";
	protected $displayGroups = array();
	final public function __construct($id=false,array $attrs=array()){
		global $registry;
		if ($id) {
			$this->id = $id;
			$this->setAttr("id",$id);
		}
		
		$this->view = $registry->ViewHandler->prepare($this->getNewView());
		$this->attrs = $attrs;
		$this->setUp();
	}
	public function setView(AbstractView $view){
		$this->view = $view;
		$this->view->form = $this;
		return $this;
	}
	public function getView(){
		return $this->view;
	}
	public function getNewView(){
		$view = new FormView("form");
		$view->form = $this;
		return $view;
	}
	public function getId(){
		return $this->id;
	}
	public function setId($id){
		$this->id = $id;
		return $this;
	}
	public function setUp(){

	}
	public function setAttrs(array $attrs){
		$this->attrs = $attrs;
		return $this;
	}
	public function setAttr($name,$value){
		$this->attrs[$name] = $value;
		return $this;
	}
	public function getAttrs(){
		return $this->attrs;
	}
	public function getAttr($name){
		return (array_key_exists($name, $this->attrs)) ? $this->attrs[$name] : null;
	}
	public function getParsedAttrs(){
		$attrs = "";
		foreach ($this->attrs as $key => $value) {
			$attrs .= " $key = '".$value."'";
		}
		return $attrs;
	}
    public function addDecorator($name,AbstractFormDecorator $decorator){
       $this->decorators[$name] = $decorator;
       return $this;
    }

    public function prepare(AbstractElement $element){
    	foreach ($this->decorators as $name=>$decorator) {
    		$dec = clone $decorator;
    		$element->addDecorator($dec,$name);
    	}
    	return $element;
    }

	function addElement(AbstractElement $element,$prepareElement=true){
		if ($prepareElement) {
			$this->prepare($element);
		}
		
		$name = $element->getId();
		$this->elements[$name] = $element;
	}
	function removeElement($element){
		if (isset($this->elements[$element])) {
			$obj = $this->elements[$element];
			unset($this->elements[$element]);
			foreach ($this->getDisplayGroups() as $dg) {
				$dg->removeElement($element);
			}
			
			return $obj;
		}
		return false;
	}
	function getElement($name){
		return (isset($this->elements[$name])) ? $this->elements[$name] : false;
	}
	function getElements(){
		return $this->elements;
	}
	function setElements($elements){
		$this->elements = $elements;
	}
	function addFormDecorator(AbstractFormDecorator $decorator){
		$decorator->setElement($this);
		$this->view->addCompiladorDecorator($decorator);
		return $this;
	}
	function setFormDecorators(array $dec){
       $this->decorators[$name] = $decorator;
       return $this;
    }
	function groupElements(array $elements,$dgName,$displayGroupClass=false){
		$displayGroupClass = (!$displayGroupClass) ? $this->displayGroupClass : $displayGroupClass;
		$dg = new $displayGroupClass($dgName);
		$dg->setParentForm($this);
		foreach ($elements as $name) {
			$el = $this->getElement($name);
			if($el){
				$dg->addElement($el);
			}
		}
		$this->displayGroups[$dgName] = $dg;
		return $dg;
	}
	function addDisplayGroup(DisplayGroup $group,$label){
		$group->setParentForm($this);
		$this->displayGroups[$label] = $group;
	}
	function removeDisplayGroup($label){
		if (isset($this->displayGroups[$label])) {
			unset($this->displayGroups[$label]);
		}
		return $this;
	}
	function inDisplayGroup($element){
		foreach($this->getDisplayGroups() as $dg){
			if ($dg->getElement($element)) {
				return true;
			}
		}
		return false;
	}
	function getDisplayGroups(){
		return $this->displayGroups;
	}
	function getDisplayGroup($name){
		return (array_key_exists($name,$this->displayGroups)) ? $this->displayGroups[$name] : null;
	}
	function populate(Array $data){
		foreach($data as $key => $value){
			if(array_key_exists($key, $this->elements)){
				$this->elements[$key]->setValue($value);
			}
		}
	}
	function getValues(){
		$values = array();
		foreach($this->elements as $key => $element){
			$values[$key] = $element->getValue();
		}
		return $values;
	}
	function getRawValues(){
		$values = array();
		foreach($this->elements as $key => $element){
			$values[$key] = $element->getRawValue();
		}
		return $values;
	}
	function isValid(){
		$errors = array();
		foreach($this->elements as $key => $element){
			if (!$element->isValid()) {
				$errors[$element->getId()] = $element->getValidateErrors();
			}
		}
		$this->validateErrors = $errors;
		if (count($errors) > 0) {
			return false;
		} else {
			return true;
		}
	}
	function isValidPartial(array $data){
		$errors = array();
		foreach($this->elements as $key => $element){
			if (array_key_exists($key, $data)) {
				$obj = clone $element;
				$obj->setValue($data[$key]);
				if (!$obj->isValid()) {
					$errors[$key] = $obj->getValidateErrors();
				}
			}
		}
		$this->validateErrors = $errors;
		if (count($errors) > 0) {
			return false;
		} else {
			return true;
		}
	}
	function getValidateErrors(){
		return $this->validateErrors;
	}

	function render(){
		if ($this->getElements()) {
			return $this->view->render();
		} else {
			return "";
		}
		
	}

}
?>
