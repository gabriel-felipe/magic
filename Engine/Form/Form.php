<?php 
namespace Magic\Engine\Form;
use Magic\Engine\Form\Decorators\AbstractFormDecorator;
use Magic\Engine\Mvc\View\Compiladores\ViewCompilador;
use Magic\Engine\Mvc\View\Compiladores\InterfaceViewCompilador;

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
	protected $replaceIds = true;
	final public function __construct($id,array $attrs=array()){
		global $registry;
		$this->id = $id;
		$this->setAttr("id",$id);
		$this->view = $registry->ViewHandler->prepare($this->getNewView());
		$this->attrs = $attrs;
		$this->setUp();
	}
	public function getNewView(){
		return new FormView("form",$this);
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
	}
    public function addDecorator(AbstractFormDecorator $decorator){
       $this->decorators[] = $decorator;
       return $this;
    }

    public function prepare(AbstractElement $element){
    	foreach ($this->decorators as $decorator) {
    		$dec = clone $decorator;
    		$element->addDecorator($dec);
    	}
    	return $element;
    }

	function addElement(AbstractElement $element){
		$this->prepare($element);
		$name = $element->getId();
		if ($this->replaceIds) {
			$element->setAttr("id",$this->getId()."-".$name);
		}
		$this->elements[$element->getName()] = $element;
	}
	function removeElement($element){
		if (isset($this->elements[$element])) {
			unset($this->elements[$element]);
		}
		return $this;
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
	function groupElements(array $elements,$dgName,$displayGroupClass=false){
		$displayGroupClass = (!$displayGroupClass) ? $this->displayGroupClass : $displayGroupClass;
		$dg = new $displayGroupClass($dgName);
		foreach ($elements as $name) {
			$el = $this->getElement($name);
			if($el){
				$dg->addElement($el);
			}
		}
		$this->displayGroups[$dgName] = $dg;
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
				$errors[$key] = $element->getValidateErrors();
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
				$obj->setValue[$data[$key]];
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
		return $this->view->render();
	}

}
?>
