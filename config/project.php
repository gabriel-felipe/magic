<?php
date_default_timezone_set("America/Sao_Paulo");
define('PROJECT_DEBUG', 1);
define('AUTO_GENERATE_LANGUAGE_URLS',true);
$themes = file_get_contents(dirname(__FILE__)."/themes.json");
$themes = json_decode($themes,true);
?>