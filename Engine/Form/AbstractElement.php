<?php 
namespace Magic\Engine\Form;
use Magic\Engine\Validator\AbstractValidator;
use Magic\Engine\Sanitizer\AbstractSanitizer;
use Magic\Engine\Mvc\View\AbstractView;
use Magic\Engine\Form\Decorators\AbstractFormDecorator;

abstract class AbstractElement {
	protected $validators=array();
	protected $validateErrors = array();
	protected $label;
	protected $sanitizers=array();
	protected $view;
	protected $value;
	protected $rawValue;
	protected $name;
	protected $required=false;
	protected $decorators = array();
	public $attrs=array();

	public function setRequired($required){
		$this->required = $required;
		return $this;
	}
	public function getRequired(){
		return $this->required;
	}
	public function setAttrs(array $attrs){
		$this->attrs = $attrs;
		return $this;
	}
	public function getAttr($name){
		return (array_key_exists($name, $this->attrs)) ? $this->attrs[$name] : null;
	}
	public function getAttrs(){
		return $this->attrs;
	}
	public function getParsedAttrs(){
		$attrs = "";
		foreach ($this->attrs as $key => $value) {
			$attrs .= " $key = '".$value."'";
		}
		return $attrs;
	}
	public function setAttr($name,$value){
		$this->attrs[$name] = $value;
		return $this;
	}
	public function getName(){
		return $this->name;
	}
	public function setId($id){
		$this->setAttr("id",$id);
	}
	public function getId(){
		return (isset($this->attrs['id'])) ? $this->attrs['id'] : $this->getName();
	}
	final function __construct($name){
		$this->setName($name);
		$this->setUp();
		return $this;
	}

	abstract function setUp();

	public function getView(){
		return $this->view;
	}

	public function setView(AbstractView $view){
		$this->view = $view;
		return $this;
	}

	public function setName($name){
		$this->name = $name;
		if (!isset($this->attrs['id'])) {
			$this->setId($name);
		}
		$this->attrs['name'] = $name;
	}
	public function setValue($value){
		$this->rawValue = $value;
		$this->sanitize();
	}
	public function getValue(){
		return $this->value;
	}
	public function getRawValue(){
		return $this->rawValue;
	}
	public function addValidator(AbstractValidator $validator){
		$this->validators[] = $validator;
		return $this;
	}
	public function addSanitizer(AbstractSanitizer $sanitizer){
		$this->sanitizers[] = $sanitizer;
		$this->sanitize();
		return $this;
	}
	public function getValidateErrors(){
		return $this->validateErrors;
	}
	public function isValid(){
		$errors = array();
		if (!$this->getValue() and !$this->getRequired()) {
			return true; //Return true if empty and not required.
		} elseif($this->getRequired() and !$this->getValue()){
			$errors['emptyField'] = "Este campo é obrigatório e não pode ser vazio.";
			$this->validateErrors = $errors;
			return false;
		}
		foreach($this->validators as $validator){
			if ($validator->isValid($this->value)) {
				continue;
			} else {
				$errors[] = $validator->getError();
			}
		}
		$this->validateErrors = $errors;
		if (count($errors) === 0) {
			return true;
		} else {
			return false;
		}
	}
	public function sanitize(){
		$value = $this->rawValue;
		foreach($this->sanitizers as $sanitize){
			$value = $sanitize->sanitize($value);
		}
		$this->value = $value;
	}

	public function addDecorator(AbstractFormDecorator $decorator,$name=false){
		$decorator->setElement($this);
		$name = (!$name) ? get_class($decorator) : $name;
		$this->decorators[$name] = $decorator;
		return $this;
	}
	public function getDecorators(){
		return $this->decorators;
	}

	public function setDecorators($dec){
		$this->decorators = $dec;
		return $this;
	}

	public function getDecorator($name){
		if (!isset($this->decorators[$name])) {
			return false;
		}
		return $this->decorators[$name];
	}
	public function render(){
		$view = clone $this->view;
		foreach ($this->getDecorators() as $key => $value) {
			$view->addCompiladorDecorator($value);
		}
		return $view->render();
	}



    /**
     * Gets the value of label.
     *
     * @return mixed
     */
    public function getLabel()
    {
        return ($this->label) ? $this->label : ucfirst($this->name);
    }



    /**
     * Sets the value of label.
     *
     * @param mixed $label the label
     *
     * @return self
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

}
?>