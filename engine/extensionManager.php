<?php 
	/**
	* 
	*/
	require_once(path_root."/engine/extension.php");
	class ExtensionManager

	{
		protected $extFolder;
		public function __construct(){
			$this->extFolder = path_root."/extensions/";
		}
		public function getExtensions(){
			return array_map(function($i){return basename($i);}, glob($this->extFolder."*", GLOB_ONLYDIR));		
		}
		public function getExtension($ext){
			$ext = basename($ext);
			if($this->hasExtension($ext)){
				$extFolder = $this->extFolder.$ext;
				if(file_exists($extFolder."/index.php")){
					include($extFolder."/index.php");
					if(class_exists($ext)){
						$obj = new $ext;
						return $obj;
					} else {
						throw new Exception("Error Processing Request, class $ext not founded", 1);
					}
				} else {
					throw new Exception("Error Processing Request, index file not founded into extension folder", 1);
				}
			} else {
				throw new Exception("Error Processing Request, extension does not exist", 1);
				
			}
		}

		public function preInstall(Extension $ext, $scope){
			$pathScope = path_root."/scopes/$scope";
			$pathLibrary = path_root."/librarys";
			if(is_dir($pathScope)){
				$replacedFiles = array();
				$newFiles = array();
				$scopeFiles = $ext->scopeFiles();
				foreach($scopeFiles as $file => $content){
					if(file_exists($pathScope."/".$file)){	
						$oldContent = file_get_contents($pathScope."/".$file);
						$replacedFiles[$pathScope."/".$file] = array($oldContent,$content);
					} else {
						$newFiles[$pathScope."/".$file] = $content;
					}
				}
				$libraryFiles = $ext->libraryFiles();
				$newLibrary = array();
				$replacedLibrary = array();
				foreach($libraryFiles as $file => $content){
					if(file_exists($pathLibrary."/".$file)){	
						$oldContent = file_get_contents($pathLibrary."/".$file);
						$replacedLibrary[$pathLibrary."/".$file] = array($oldContent,$content);
					} else {
						$newLibrary[$pathLibrary."/".$file] = $content;
					}
				}
				$querys = $ext->installQueries();
				return array(
					"scopeFiles"=>array("new"=>$newFiles,"replaced"=>$replacedFiles),
					"libraryFiles"=>array("new"=>$newLibrary,"replaced"=>$replacedLibrary),
					"queries"=>$querys
					);
			} else {
				throw new Exception("Error Processing Request, Scope does not exist.", 1);				
			}
		}
		public function install($ext,$scope){
			try {
				$ext->validateInstall();
			} catch (Exception $e) {
				throw new Exception("Error Processing Request, extensão não passou na validação. Erro: ".$e->getMessage(), 1);
				return false;
			}
			$pathScope = path_root."/scopes/$scope";
			$pathLibrary = path_root."/librarys";
			if(is_dir($pathScope)){
				$querys = $ext->installQueries();
				if(is_array($querys) and $querys){
					try{
						$dbmanager = new bffdbmanager;
					} catch (Exception $e){
						die("Erro com o banco e a extensao requer queries, instalação abortada");
					}
					foreach($querys as $query){
						$dbmanager->query($query);
					}
				}
				$scopeFiles = $ext->scopeFiles();
				foreach($scopeFiles as $file => $content){
					$file = $pathScope."/".$file;
					if(!is_dir(dirname($file))){
						mkdir(dirname($file),0775,true);
					}
					$handler = fopen($file,"w+");
					fwrite($handler, $content);
					fclose($handler);
				}
				$libraryFiles = $ext->libraryFiles();
				foreach($libraryFiles as $file => $content){
					$file = $pathLibrary."/".$file;
					$handler = fopen($file,"w+");
					fwrite($handler, $content);
					fclose($handler);
				}
				return true;
			} else {
				throw new Exception("Error Processing Request, Scope does not exist.", 1);				
			}
		}

		public function hasExtension($ext){
			$ext = basename($ext);

			return file_exists($this->extFolder.$ext);
		}
	}

?>