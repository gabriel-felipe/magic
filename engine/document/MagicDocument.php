<?php 
/**
 * @package MagicDocument
 **/


/**
 * Responsável por gerar o html da página e gerenciar os assets (Css's / JS's) da página.
 * Compõe todo o head e seus metadados e outras informações relacionadas.
 * @property protected $registry utiliza o registrador geral do Magic para poder acessar e registrar informações de outras partes do sistema
 * 
 * @property array $data armazena informações para serem utilizadas no layout.html.
 * 
 * @property string $layout, caminho até o layout do html geral.
 * 
 */
 class MagicDocument
 {
 	protected $registry,$data=array(),$layout="/common/template/layout.html";
 	/**
 	 * @param $registry sets MagicDocument::registry = $registry
 	 */
 	function __construct($registry)
 	{
 		$this->registry = $registry;
 		$this->title = "";
		$this->bodyId = "page";
		$this->bodyClass = "";
		$this->holderClass = "";
		$this->holderId = "all-site";
		$this->doctype = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
		$this->lang = "pt-br";
 	}

 	public function setLayout($layout){
 		$this->layout = $layout;
 	}

 	public function getLayout(){
 		return $this->layout;
 	}

 	public function getMetas(){
 		return $this->MetaManager->getMetas();
 	}

 	public function addLink($link){
 		return $this->LinkManager->addLink($link);
 	}

 	public function getLinks(){
 		return $this->LinkManager->getLinks();
 	}

 	public function addScript($script){
 		return $this->ScriptManager->addScript($script);
 	}

 	public function getScripts($position=false){
 		return $this->ScriptManager->getScripts($position);
 	}

 	public function render($conteudo){
 		ob_start();
		include(path_root.$this->layout);
        $content = ob_get_clean();
        return $content;
 	}

 	public function __set($name,$value){
 		$this->data[$name] = $value;
 	}

 	public function __get($name){
 		if (isset($this->data[$name])) {
 			return $this->data[$name];
 		}
 		return $this->registry->get($name);
 	}



 } 
?>