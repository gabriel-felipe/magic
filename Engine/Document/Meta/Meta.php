<?php 
namespace Magic\Engine\Document\Meta;
/**
 * @package MagicDocument\Script
 **/

/**
 * Classe que simula uma meta tag
 * @property string $alias um apelido para a metatag, para você poder referenciar ela mais tarde.
 * @property string $content o conteúdo da meta, tudo que vai entre <meta />
 */
class Meta
{
	protected $alias,$content;

	public function __construct($alias,$content){
		$this->alias = $alias;
		$this->content = $content;
	}

	public function getAlias(){
		return $this->alias;
	}

	public function setAlias($alias){
		$this->alias = $alias;
	}

	public function getContent(){
		return $this->alias;
	}

	public function setContent($content){
		$this->content = $content;
	}

	public function toString(){
		return "<meta ".$this->content." />";
	}

	public function __toString(){
		return $this->toString();
	}
}
?>