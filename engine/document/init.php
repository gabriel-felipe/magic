<?php 
require_once(path_engine."/document/MagicDocument.php");
require_once(path_engine."/document/AbstractAsset.php");
require_once(path_engine."/document/link/LinkAbstract.php");
require_once(path_engine."/document/link/LinkManager.php");
require_once(path_engine."/document/link/css/lessc.php");
require_once(path_engine."/document/link/css/CssAbstract.php");
require_once(path_engine."/document/link/css/CommonCss.php");
require_once(path_engine."/document/link/css/ScopeCss.php");
require_once(path_engine."/document/link/css/CacheCss.php");
require_once(path_engine."/document/link/css/ExternalCss.php");
require_once(path_engine."/document/script/ScriptAbstract.php");
require_once(path_engine."/document/script/ScriptManager.php");
require_once(path_engine."/document/script/CommonJs.php");
require_once(path_engine."/document/script/ScopeJs.php");
require_once(path_engine."/document/meta/MetaManager.php");
require_once(path_engine."/document/meta/Meta.php");

$less = new lessc;
$registry->set("less",$less);

$LinkManager = new LinkManager;
$registry->set("LinkManager",$LinkManager);

$MetaManager = new MetaManager;
$registry->set("MetaManager",$MetaManager);
$MetaManager->addMeta(new Meta("contentType","http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\""));
$MetaManager->addMeta(new Meta("viewport","name=\"viewport\" content=\"width=device-width,initial-scale=1\""));
$ScriptManager = new ScriptManager;
$registry->set("ScriptManager",$ScriptManager);



$MagicDocument = new MagicDocument($registry);
$registry->set("html",$MagicDocument);

$magicCss = new CommonCss("magic.css");
$magicCss->gridColumns = $gridColumns;
$magicCss->gridMargin = $gridMargin;
$magicCss->breakpoints = $breakpoints;

$LinkManager->addLink($magicCss);

$ScriptManager->addScript(new CommonJs("jquery-1.9.1.js","top"));
$ScriptManager->addScript(new CommonJs("majax.js","top"));

?>