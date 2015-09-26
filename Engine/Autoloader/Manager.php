<?php
namespace Magic\Engine\Autoloader;
use \Exception;
final class Manager {

	static protected $_instance;
	static protected $_autoloaders = array();
	
	protected function __construct(){
        spl_autoload_register(array(__CLASS__, 'autoload'));
	}

	public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
            $scopes = glob(path_root."/Scopes"."/*",GLOB_ONLYDIR);
        	$scopes = array_map("basename",$scopes);
        	foreach ($scopes as $scope) {
        		self::registerAutoloader(new PrefixDir($scope,"/Scopes/".$scope));
        	}

            
        }
        return self::$_instance;
    }

    public static function registerAutoloader(InterfaceAutoloader $autoloader){
    	self::$_autoloaders[] = $autoloader;
    	return true;
    }

    public static function autoload($class){    	
    	foreach (self::$_autoloaders as $loader) {

    		if ($loader->autoload($class)) {
    			return true;
    		}
    	}
    	if (self::_autoload($class)) {
    		return true;
    	}
		
    	print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
    	echo "Classe não carregada: $class";
    	die();
    	
    }
    /* Backup autoloader */
    public static function _autoload($class){
    	$d = DIRECTORY_SEPARATOR;
	    // project-specific namespace prefix
	    $prefix = 'Magic'."\\";

	    // base directory for the namespace prefix
	    // does the class use the namespace prefix?
	    $len = strlen($prefix);
	    if (strncmp($prefix, $class, $len) !== 0) {
	        // no, move to the next registered autoloader
	        return;
	    }

	    // get the relative class name
	    $relative_class = substr($class, $len);

	    // replace the namespace prefix with the base directory, replace namespace
	    // separators with directory separators in the relative class name, append
	    // with .php
	    $file = path_root . $d . str_replace('\\', $d, $relative_class) . '.php';
	   
	    // if the file exists, require it
	    if (file_exists($file)) {
	    	if (file_exists($file)) {
	    		require $file;
	    		return true;
	    	}
	    }
	    echo "Class $class was not found and therefore not loaded.";
	    die();
	    return false;
    }
}