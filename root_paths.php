<?php 
$path = str_replace($_SERVER["DOCUMENT_ROOT"], "",dirname(__FILE__));
define("path_root",$_SERVER["DOCUMENT_ROOT"]."$path");
define("base_url","http://".$_SERVER["SERVER_NAME"]."$path");
define("path_base","http://".$_SERVER["SERVER_NAME"]."$path");
define("path_cache",path_root."/cache");
define("path_scopes",path_root."/scopes");
define("path_uploads",path_root."/uploads");
define("path_library",path_root."/librarys");
define("path_engine",path_root."/engine");
define("path_engine_library",path_root."/engine/library");
define("path_datamgr",path_root."/datamgr");
define("path_system_js",path_root."/engine/js");
define("path_common_js",path_root."/common/js");
define("path_common",path_root."/common");
define("base_cache",path_base."/cache");
define("path_log",path_root."/logs");
define("path_engine_css",path_root."/engine/css");
define("path_common_css",path_root."/common/css");
define("base_js_engine",path_base."/engine/js");

?>