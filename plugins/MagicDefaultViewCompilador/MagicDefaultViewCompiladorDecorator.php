<?php 
/**
* Compilador padrão de views do Magic
*/
class MagicDefaultViewCompiladorDecorator extends AbstractViewCompiladorDecorator
{
	protected $data;
	public function compilar($conteudo)
	{
		$this->data = $this->view->getData();
		// extract($this->data);
		ob_start();
        eval("?> ".$conteudo. "<?php ");
        $conteudo = ob_get_clean();
        return $conteudo;
	}
	function __get($name){
		global $registry;
		return (isset($this->data[$name])) ? $this->data[$name] : $registry->get($name);
	}
}
?>