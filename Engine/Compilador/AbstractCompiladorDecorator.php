<?php 
	namespace Magic\Engine\Compilador;
	/**
	* Decorator para compilar assets
	*/
	abstract class AbstractCompiladorDecorator implements InterfaceCompilador
	{
		protected $compilador;
		public function setCompilador(InterfaceCompilador $compilador){
			$this->compilador = $compilador;
		}
		public function getCompilador(){
			return $compilador;
		}
		public function compilarTodos($conteudo){
			return $this->compilar($this->compilador->compilarTodos($conteudo));
		}
		function compilar($conteudo){}
	}
?>