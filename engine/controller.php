<?php
	abstract class controller {
		//Variável responsável por classes e informaçadicionais
		public $registry;
		//Definindo outras Variáveis
		protected $requires;
		protected $datamgr;
		protected $output;
		//Definindo Variáveis de tratamento de erro
		protected $errors;
		protected $warnings;
		//Definindo Variáveis de conteudo
		protected $data;
		public $children;
		private $template;
		protected $pathsTemplates;

		public function __construct($registry=array()){
			$this->before_construct();
			$this->registry = $registry;	
			$this->requires = array();
			$this->errors = array();
			$this->warnings = array();
			$this->data = array();
			$this->children = array();
			$this->pathsTemplates = array(
				"default"=>path_template."/",
				"common" =>path_root."/common/templates/");
			$this->after_construct();
		}
		//Funções gerenciamento de links de css
		
		//Triggers
		protected function before_construct(){

		}
		protected function after_construct(){

		}

		protected function before_get_content(){

		}

		//Funções gerenciamento de erros;
		public function print_errors(){
			foreach($this->warnings as $warning){
				echo "<strong>Warning:</strong> $warning <br />";
			}
			if(count($this->errors) > 0){
				foreach($this->errors as $error){
				echo "<strong>Fatal Error:</strong> $error <br />";
				}
				
				die();
			}
		}
		public function add_warning($warning="", $line=__LINE__, $file=__FILE__){
			$this->warnings[] = $warning." on file $file at line $line";
		}

		protected function getChild($child, $args = array()) {

			$action = new Action($child, $args,$this->registry);
			ob_start();
				$action->execute();
			$exec = ob_get_clean();
			
					

			return $exec;
		}
		public function add_children($children){
			$this->children[] = $children;
		}
		public function get_view($viewFile,$data=array()){
			if (file_exists($viewFile . '.tpl')) {
				extract($data);
				
	      		ob_start();
	      
		  		require($viewFile . '.tpl');
	      
		  		$this->output = ob_get_contents();

	      		ob_end_clean();
	      		
				return $this->output;
	    	} else {
				trigger_error('Error: Could not load view ' . $viewFile . '.tpl !');
				exit();				
	    	}
		}
		protected function get_content() {
			$this->before_get_content();
			global $breakpoints, $gridColumns;
			foreach ($this->children as $child) {
				$name = explode("/",$child);
				$name = end($name);
				$name = $name;
				$content = $this->getChild($child);				
				$this->data[$name] = $content;
			}
			
			return $this->get_view($this->template,$this->data);
		}
		
	    //Funções extras
	    public function get_requires(){
   			global $path_root, $path_base, $path_models, $path_js, $path_css, $path_common, $path_controllers,$path_datamgr;

			foreach($this->requires as $require){
				require_once($require);
			}
		}
		public function get_data(){
	
	    }
	    protected function set_template($template,$pathAlias="default"){
	    	if (isset($this->pathsTemplates[$pathAlias])) {
	    		$this->template = $this->pathsTemplates[$pathAlias].$template;
	    		return true;
	    	} else {
	    		throw new Exception("Caminho ($pathAlias) para templates não foi encontrado.", 1);
	    		
	    	}

	    	
	    }
	    public function __get($key) {
			return $this->registry->get($key);
		}
	}	
?>