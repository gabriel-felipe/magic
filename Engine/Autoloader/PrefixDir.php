<?php 
namespace Magic\Engine\Autoloader;
class PrefixDir implements InterfaceAutoloader
{
	protected $prefix;
	protected $directory;
	protected $rootDirectory;
	public function __construct($prefix,$directory,$rootDirectory=null){
		$this->prefix = $prefix;
		$this->directory = $directory;
		if (!$rootDirectory) {
			$this->rootDirectory = path_root;
		} else {
			$this->rootDirectory = rtrim($rootDirectory,DIRECTORY_SEPARATOR);
		}

	}

	public function autoload($class){
		$d = DIRECTORY_SEPARATOR;
	    // project-specific namespace prefix
    
	    $prefix = $this->prefix."\\";

	    // base directory for the namespace prefix
	    // does the class use the namespace prefix?
	    $len = strlen($prefix);
	    if (strncmp($prefix, $class, $len) !== 0) {
	        // no, move to the next registered autoloader
	        return false;
	    }

	    // get the relative class name
	    $relative_class = substr($class, $len);

	    // replace the namespace prefix with the base directory, replace namespace
	    // separators with directory separators in the relative class name, append
	    // with .php
	    $file = $this->rootDirectory. $d . $this->directory . $d . str_replace('\\', $d, $relative_class) . '.php';
	   
	    // if the file exists, require it
	    if (file_exists($file)) {
	        require $file;
	        return true;
	    }
	    return false;
	}

	public function match($class){

	}
}
?>