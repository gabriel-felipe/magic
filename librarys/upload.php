<?php

	class upload {
		public $extensions = "*";
		public $outputDir;
		public $outputName;
		public $inputFile;
		public $errors = array();
		public function __construct(){
			$this->extensions = "*";
			$this->outputDir = path_uploads."/";
			$this->outputName = rand()."_".time();
		}
		public function up($inputFile, $outputName=false){
			$this->inputFile = $inputFile;
			if($outputName){
				$this->outputName = $outputName;
			}
			if(isset($this->inputFile) and $this->inputFile['name']){
				$ext = end(explode(".", $this->inputFile['name']));
				if(count(@preg_grep("/$ext/i", $this->extensions)) > 0 or $this->extensions == "*"){
					if(!is_dir($this->outputDir)){
						if(mkdir($this->outputDir, 0777, true) ){
						} else{
							$this->errors[] = "The output folder doesn't exist and i can't create it";
							return false;
						}
					} 
					
					if(move_uploaded_file($this->inputFile['tmp_name'], $this->outputDir.$this->outputName.".$ext") ) {
						return $this->outputDir.$this->outputName.".$ext";
					} else {
						$this->errors[] = "Fail to move uploaded file";
						return false;
					}
				} else {
					$this->errors[] = "File input doesn't have the required extensions";
					return false;
				}
			} else {
				$this->errors[] = "Input file unset or empty.";
				return false;
			}
		}
		public function multipleUp($files, $callbackName=false){
			$this->inputFile = $files;
			$nomes = array();
			foreach($this->inputFile['name'] as $i=>$filename){
				$extensao = end(explode(".", $filename));
				$filename = str_replace(".$extensao", "", $filename);
				$this->outputName = (is_callable($callbackName)) ? $callbackName($filename) : $filename;
				if(isset($this->inputFile) and $this->inputFile['name'][$i]){
					$ext = end(explode(".", $this->inputFile['name'][$i]));
					if(count(@preg_grep("/$ext/i", $this->extensions)) > 0 or $this->extensions == "*"){
						if(!is_dir($this->outputDir)){
							if(mkdir($this->outputDir, 0777, true) ){
							} else{
								$this->errors[] = "The output folder doesn't exist and i can't create it";
							}
						} 
						
						if(move_uploaded_file($this->inputFile['tmp_name'][$i], $this->outputDir.$this->outputName.".$ext") ) {
							$nomes[] = $this->outputDir.$this->outputName.".$ext";
						} else {
							$this->errors[] = "Fail to move uploaded file";
							
						}
					} else {
						$this->errors[] = "File input doesn't have the required extensions";
					}
				} else {
					$this->errors[] = "Input file unset or empty.";
				}
			} 
			if(count($this->errors) > 0){
				return false;
			} else {
				return $nomes;
			}
		}
	}
?>
	