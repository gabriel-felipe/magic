<?php 
namespace Magic\Engine\Scope;
use Magic\Engine\Scope\Assets\ThemeView;
use Magic\Engine\Scope\Assets\ThemeJs;
use Magic\Engine\Scope\Assets\ScopeJs;
use Magic\Engine\Scope\Assets\ScopeCss;
use Magic\Engine\Scope\Assets\ThemeCss;
use Magic\Engine\Language;
use \Exception;
use \data;
/**
* Classe responsável por gerenciar um escopo.
*/
class Scope
{
	protected $name;
	protected $folder;
    protected $base;
	protected $scopesFolder="/Scopes";
	protected $registry;
    public $language;
    protected $themes=array();
	function __construct($name,$registry)
	{
		$this->registry = $registry;
		$this->setName($name);
        $this->setThemes($this->config->themes->get($this->getName()));
	}
    function initLanguage(){
        $language = new Language($this->registry,$this->getLanguageFolder());
        $language->init("br"); //If exist language br, that will be the default one, else it will select the first in alphabetical order.
        $magic_language = data::get("magic_language");
        if($magic_language){
            $language->select($magic_language);
        }
        $this->language = $language;
        
    }
    function setThemes($themes=false){
        if (!$themes) {
            $themes = array("default");
        } elseif (is_string($themes)) {
            $themes = array($themes);
        }
        $this->themes = $themes;
    }
    function prependTheme($theme){
        array_unshift($this->themes,$theme);
    }
    function appendTheme($theme){
        array_push($this->themes,$theme);
    }
    function getCss($file){
        return new ScopeCss($file,$this);
    }
    function getThemeCss($file){
        return new ThemeCss($file,$this);
    }
    function getJs($file){
        return new ScopeJs($file,$this);
    }
    function getThemeJs($file){
        return new ThemeJs($file,$this);
    }
    function getView($file=false){
        return $this->ViewHandler->prepare(new ThemeView($file,$this));
    }
	/**
	 * Carrega o arquivo init.php localizado dentro da pasta $this->folder.
	 * @return $this;
	 */
	function init(){
        foreach ($this->themes as $theme) {
            if(!is_dir($this->getThemeFolder($theme))){
                throw new \LogicException("Theme $theme Does not exist: ".$this->getThemeFolder($theme), 1);
            }
        }
        
		if (is_file($this->getFolder()."/init.php")) {
			require_once($this->getFolder()."/init.php");
		}  
        $this->initLanguage();


	}

    public function getThemes(){
        return $this->themes;
    }

    public function getMainTheme(){
        $themes = $this->getThemes();
        return $themes[0];
    }

    /**
     * Gets the value of name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value of name.
     *
     * @param mixed $name the name
     *
     * @return self
     */
    public function setName($name)
    {
    	$folder = $this->scopesFolder."/$name";
    	if (is_dir(path_root.$folder)) {
    		$this->name = $name;
    		$this->setFolder($folder);
            $this->setBase(path_base.$this->scopesFolder."/$name");
        	return $this;
    	} else {
    		throw new \UnexpectedValueException("Escopo $name não foi encontrado dentro da pasta scopes, cheque sua existência.", 1);
    	}
        
    }
    public function setBase($base){
        $this->base = $base;
    }

    public function getBase(){
        return $this->base;
    }
    /**
     * Gets the value of folder.
     *
     * @return mixed
     */
    public function getFolder($pathRoot=true)
    {
        $pathRoot = ($pathRoot) ? $pathRoot : "";
        return path_root.$this->folder;
    }

    public function getModelFolder(){
        return $this->getFolder()."/model";   
    }

    public function getControllerFolder()
    {
        return $this->getFolder()."/controller";
    }

    public function getViewFolder(){
        return $this->getFolder()."/views";
    }

    public function getViewBase(){
        return  $this->getBase()."/views";
    }

    public function getLanguageFolder(){
        return $this->getFolder()."/language";
    }

    /**
     * Sets the value of folder.
     *
     * @param mixed $folder the folder
     *
     * @return self
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;

        return $this;
    }
    //Returns primary theme folder
    public function getThemeFolder($theme=false){
        if (!$theme) {
            $theme = $this->getMainTheme();
        }
        return $this->getViewFolder()."/".$theme;
    }
    public function getThemeBase($theme=false){
        if (!$theme) {
            $theme = $this->getMainTheme();
        }
        return $this->getViewBase()."/".$theme;
    }
    public function getTemplateFolder($theme=false){
        return $this->getThemeFolder($theme)."/template";
    }
    public function getTemplateBase($theme=false){
        return $this->getThemeBase($theme)."/template";
    }
    public function getImageBase($theme=false){
        return $this->getThemeBase($theme)."/image";
    }
    public function getThemeImg($img){
        $c = 0;
        foreach ($this->getThemes() as $theme) {
            $c++;
            $path = "/Scopes/".$this->getName()."/views/".$theme."/image/$img";
            $file = path_root.$path;
            
            if (file_exists($file)) {
                return path_base.$path;
            } else if ($c == count($this->getThemes())) {
                return false;
            }
        }
    }

    function __get($name){
    	return $this->registry->get($name);
    }
}
?>