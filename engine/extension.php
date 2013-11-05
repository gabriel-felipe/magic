<?php 
	
	abstract class Extension {
		protected $name;
		protected $description;
		protected $version;
		protected $dependencies = array();
		protected $tables = array(); //Tables that the queries use so we can generate a backup.
		//Only install extension if this function doesnt throw any exception
		public function validateInstall(){
			throw new Exception("Error Processing Request VALIDACAO BUG", 1);
			
		}
		/*What files should be generated into the scope folder? Should return an array in this pattern:

		array(
			"filepath/name" => "fileContent"
		)
		path will be prepended by the path of the scope of instalation
		*/
		public function scopeFiles(){
			return array();
		}
		/*What files should be generated into librarys folder? Should return an array in this pattern:

		array(
			"filepath/name" => "fileContent"
		)
		path will be prepended by the path of the scope of instalation
		*/
		public function libraryFiles(){
			return array();
		}

		/*Querys that should be executed, return array of queries*/
		public function querys(){
			return array();
		}

		/*Should return an array of modifications to be performed, the array must respect the following structure: 
		array(
			"fileToMod" => array(
				array (
					"search" => "345", //What should we search for in the file? The parameter is regex based and searchs only one line
					"offset" => "1", //Mod is limited to a single-line search, but you can use the "offset" attribute to blindly blanket additional lines of code in the replace. 
					"op" => "before", //Kind of operation, in this case insert before. Possible operations are: before, after, replace
					"content" => "12" //Content that will be added with this operation
				)
			) 
		)
		*/
		public function mods(){
			return array();
		}
		/*procedures to do at uninstall */
		public function uninstall(){
			return false;
		}

		//Getters
		public function getName(){
			return $this->name;
		}
		public function getVersion(){
			return $this->version;
		}
		public function getDescription(){
			return $this->description;
		}
		public function getInfo(){
			return array(
				"name" => $this->name,
				"description" => $this->description,
				"version" => $this->version,
				"dependencies" => $this->dependencies,
			);
		} 


	}

?>
