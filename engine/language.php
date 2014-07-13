<?php 
/**
* Class para gerenciamento multi-linguagem;
*/
class Language
{
    public $registry;
    public $languageFolder;
    protected $languages = array();
    protected $language = false;
    public $data = array();
    public function __construct($registry=array())
    {   
        $this->registry = $registry;
    }
    public function init($defaultLanguage=""){
        $this->languageFolder = path_scope."/language";
        if(!is_dir($this->languageFolder)){
            throw new Exception("No language folder founded.", 1);
        }
        $this->updateLanguages();
        try {
            $this->select($defaultLanguage);
        } catch (Exception $e) {
            $this->select(reset($this->languages));
        }
    }
    public function updateLanguages()
    {
        $languages = glob($this->languageFolder."/*",GLOB_ONLYDIR);
        $languages = array_map("basename",$languages);
        $this->languages = $languages;
    }
    public function generateUrls(){
        $scopes = glob(path_scopes."/*",GLOB_ONLYDIR);
        $scopes = array_map("basename",$scopes);
        $languagesScopes = array();
        $scs = $this->url->getShortcuts(); //create URL Multi Language;
        $finalScs = array();
        foreach($scopes as $scope){
            $languages = glob(path_scopes."/$scope/language/*",GLOB_ONLYDIR);
            $languages = array_map("basename",$languages);
            foreach($languages as $lang){
                foreach($scs as $k=>$sc){
                    $sc_copy = $sc;
                    if($sc['scope'] != $scope){
                        continue;
                    }
                    if(!isset($sc_copy['defaults']) or !is_array($sc_copy['defaults'])){
                        $sc_copy['defaults'] = array();
                    }
                    $sc_copy['defaults']['magic_language'] = $lang;
                    $sc_copy['route'] = rtrim($sc['route'],"/");
                    $key = ltrim(rtrim($k,"/")."/".$lang,"/");
                    $finalScs[$key] = $sc_copy;
                    $this->url->add_shortcut($key,$sc_copy);
                }
            }
        }
    }

    public function select($lang){
        if(in_array($lang, $this->languages)){
            $this->language = $lang;
            if(is_file($this->languageFolder."/".$this->language."/index.php")){
                $this->load("index");
            }
            return $this->data;
        } else {
            throw new Exception("Language not found.", 1);
        }
    }

    public function load($file){
        $file = $this->languageFolder."/".$this->language."/".$file.".php";
        if(is_file($file)){
            $_ = array();
            require($file);
            $this->data = array_merge($this->data,$_);
            return $_;
        } else {
            throw new Exception("Language file not founded.", 1);
        }
    }
    public function getLang(){
        return $this->language;
    }
    public function get($key){
        return (isset($this->data[$key])) ? $this->data[$key] : $key;
    }
    public function getData(){
        return $this->data;
    }

    public function __get($key) 
    {
        return $this->registry->get($key);
    }
}
?>