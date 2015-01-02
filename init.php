<?php


if(session_id() == ""){
session_start();
}
$jsonConfigs = array("database");
foreach($jsonConfigs as $fileConfig){

${$fileConfig} = $globalConfig->{$fileConfig}->getData();

}

foreach($database as $constant=>$value)
    define($constant,$value);

$path = str_replace($_SERVER["DOCUMENT_ROOT"], "",dirname(__FILE__));
// Path Constants

if($scope === false){
    $error = $registry->get("htmlError");
    $error->trigger("404");
    die();
}

?>