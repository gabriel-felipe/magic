<?php
	namespace Magic;
	use Magic\Engine\Config\MagicConfig;
	use Magic\Engine\Registry;	
	if(session_id() == ""){
		session_start();
	}
	$root = str_replace("/",DIRECTORY_SEPARATOR,$_SERVER["DOCUMENT_ROOT"]);
	$path = str_replace($root, "",dirname(__FILE__));
	define("path_root",$root."$path");
	define("base_url","http://".$_SERVER["SERVER_NAME"]."$path");
	define("path_base","http://".$_SERVER["SERVER_NAME"]."$path");

	spl_autoload_register(function ($class) {
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
	        require $file;
	    } else {
	    	print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
	    	echo "Arquivo não existe: $file";
	    	die();
	    }
	});
	


	define('APPLICATION_ENV', getenv("APPLICATION_ENV"));
	$globalConfig = new MagicConfig;
	$globalConfig->addFolder(path_root."/Config/defaults");
	$globalConfig->addFolder(path_root."/Config/".APPLICATION_ENV);
	$globalConfig->loadInAllAs("db.json","database");
	$globalConfig->loadInAllAs("routes.json","routes");
	$globalConfig->loadInAllAs("themes.json","themes");
	$globalConfig->loadInAllAs("errors.json","errors");
	$globalConfig->loadInAll("config.json");

	if($globalConfig->projectDebug == 1){
		ini_set('display_errors',1); 
	 	error_reporting(E_ALL);
	 	ini_set('html_errors', 'On');
 	}
 	date_default_timezone_set($globalConfig->default_timezone);
 	define("AUTO_GENERATE_LANGUAGE_URLS", $globalConfig->auto_generate_language_urls);

	$registry = new Registry;
	$registry->set("config",$globalConfig);
	//Registering Hooks
	use Magic\Engine\Hooks\HookChainManager;
	$hooks = new HookChainManager;
	$registry->set("hooks",$hooks);
	use Magic\Engine\Datamgr\DbConnect;
	$dbConnect = new DbConnect();
	$registry->set("DbConnect",$dbConnect);
	
	//Registering Document
	use Magic\Engine\Document\Link\LinkManager;
	use Magic\Engine\Document\Append\AppendManager;
	use Magic\Engine\Document\Meta\MetaManager;
	use Magic\Engine\Document\Script\ScriptManager;
	use Magic\Engine\Document\MagicDocument;
	use Magic\Engine\Document\DocumentError;
	$LinkManager = new LinkManager;
	$registry->set("LinkManager",$LinkManager);

	$AppendManager = new AppendManager;
	$registry->set("AppendManager",$AppendManager);

	$MetaManager = new MetaManager;
	$registry->set("MetaManager",$MetaManager);

	$BottomScriptManager = new ScriptManager;
	$registry->set("BottomScriptManager",$BottomScriptManager);

	$TopScriptManager = new ScriptManager;
	$registry->set("TopScriptManager",$TopScriptManager);

	$MagicDocument = new MagicDocument($registry);
	$registry->set("html",$MagicDocument);

	$DocumentError = new DocumentError($registry);
	$registry->set("htmlError",$DocumentError);

	//Loading Library for data clean
	require_once('Engine/Library/data-cleaner.php');
	
	//Loading View Handler
	use Magic\Engine\Mvc\View\ViewHandler;
	$ViewHandler = new ViewHandler($registry);
	$registry->set("ViewHandler",$ViewHandler);

	//Loading Plugin
	

	//Loading Loader.
	use Magic\Engine\Loader;
	$loader = new loader($registry);
	$registry->set('load',$loader);

	
	//Loading Urls
	use Magic\Engine\Mvc\Url;
	use Magic\Engine\Language;
	use \data;
	$routes = $globalConfig->routes->getData();
	$url = new Url;
	$url_amigavel = data::get('url','url');
	foreach ($routes as $scopeb => $rts) {
		foreach ($rts as $route => $params) {
			$params['scope'] = $scopeb;
			$url->addShortcut($route,$params);
		}
	}
	$registry->set("url",$url);

	if(AUTO_GENERATE_LANGUAGE_URLS){
		Language::generateUrls();
	}
	
	if(!isset($_GET['scope'])){
		if(isset($_POST['scope'])) {
			$_GET['scope'] = $_POST['scope'];
		} else {
			$url->analyze($url_amigavel);	
		}
	}

	$scope = data::get('scope','url');
	//Quanto refatorar o banco de dados tornar isso desnecessário.
	$jsonConfigs = array("database");
	foreach($jsonConfigs as $fileConfig){

	${$fileConfig} = $globalConfig->{$fileConfig}->getData();

	}

	foreach($database as $constant=>$value)
	    define($constant,$value);

	

	if($scope === false){
	    $error = $registry->get("htmlError");
	    $error->trigger("404");
	    die();
	}

	//Inicializando o escopo
	use Magic\Engine\Scope\Scope;
	$scope = new Scope($scope,$registry);
	$url->setScope($scope->getName());
	$registry->set("scope",$scope);
	$scope->init();

	define("magic_language",$scope->language->getLang());
	
	
	if(array_key_exists("route", $_GET)){
		$route = $_GET['route'];
	} elseif (array_key_exists("route", $_POST)){
		$route = $_POST['route'];
	}
	$route = str_replace("_","/",$route);
	$registry->set("route",$route);


	//Criando e executando a action
	use Magic\Engine\Mvc\Action;
	$action = new Action($route,$scope,array(),$registry);	
	$registry->set("action",$action);
	$action->execute();