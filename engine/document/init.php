<?php 
require_once(path_engine."/document/Response.php");
require_once(path_engine."/document/MagicDocument.php");
require_once(path_engine."/document/AbstractAsset.php");
require_once(path_engine."/document/AbstractAssetManager.php");
require_once(path_engine."/document/link/LinkAbstract.php");
require_once(path_engine."/document/link/LinkManager.php");
require_once(path_engine."/document/link/FavIconLink.php");
require_once(path_engine."/document/link/css/CssAbstract.php");
require_once(path_engine."/document/link/css/CommonCss.php");
require_once(path_engine."/document/link/css/CacheCss.php");
require_once(path_engine."/document/link/css/ExternalCss.php");
require_once(path_engine."/document/script/ScriptAbstract.php");
require_once(path_engine."/document/script/ScriptManager.php");
require_once(path_engine."/document/script/CommonJs.php");
require_once(path_engine."/document/script/CacheJs.php");
require_once(path_engine."/document/script/ExternalJs.php");
require_once(path_engine."/document/meta/MetaManager.php");
require_once(path_engine."/document/meta/Meta.php");
require_once(path_engine."/document/append/AppendManager.php");
require_once(path_engine."/document/append/AppendAbstract.php");
require_once(path_engine."/document/append/JsAppend.php");
require_once(path_engine."/document/DocumentError.php");

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
?>
