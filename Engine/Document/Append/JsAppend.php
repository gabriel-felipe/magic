<?php 
namespace Magic\Engine\Document\Append;
/**
 * @package MagicDocument\Appends
 **/
 
/**
* Classe para adicionar javascripts no final do body.
*/
class JsAppend extends AppendAbstract
{
	function __construct($alias,$content){
		parent::__construct($alias,$content,"body");
	}
	function setContent($content){
		$this->content = "<script>".$content."</script>";
	}
}
?>