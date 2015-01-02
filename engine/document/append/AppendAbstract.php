<?php 
/**
 * @package MagicDocument\Appends
 **/

/**
 * Classe responsável por objetos que serão incluidos em partes do html.
 * @property string $alias um apelido para a metatag, para você poder referenciar ela mais tarde.
 * 
 * @property string $content o conteúdo da meta, tudo que vai entre <meta />
 * 
 * @property boolean $prepend Quando ativa o bloco de conteúdo é inserido no início da seção. 
 * Do contrário é inserido no final.
 * 
 * @property string $position define qual a posição do script. Os valores de $this->positions são as possibilidades.
 * 
 * @property array $positions define todas as posições possíveis.
 */
class AppendAbstract
{
	protected $alias,$content,$position="head";
	protected $prepend=0;
	protected $positions = array("head","body","site-holder");
	public function __construct($alias,$content,$position="head"){
		$this->setAlias($alias);
		$this->setContent($content);
		$this->setPosition($position);
	}

	public function getAlias(){
		return $this->alias;
	}

	public function setAlias($alias){
		$this->alias = $alias;
	}

	public function getPrepend(){
		return $this->prepend;
	}

	public function setPrepend($prepend){
		$this->prepend = $prepend;
	}

	public function getContent(){
		return $this->alias;
	}

	public function setContent($content){
		$this->content = $content;
	}

	/**
	 * Define $this->position confore o parâmetro position.
	 */
	public function setPosition($position){
		if (!in_array($position, $this->positions)) {
			throw new Exception("Posição $position para append não existe", 1);	
			return false;
		} else {
			$this->position = $position;
			return true;	
		}
		
	}

	public function getPosition(){
		return $this->position;
	}

	public function toString(){
		return $this->content;
	}

	public function __toString(){
		return $this->toString();
	}
}
?>