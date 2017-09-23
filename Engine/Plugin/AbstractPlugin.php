<?php 
namespace Magic\Engine\Plugin;
use \LogicException;
use Magic\Engine\Registry;
use Magic\Engine\Config\Config;
use Magic\Engine\Plugin\Assets\PluginJs;
use Magic\Engine\Plugin\Assets\PluginCss;
/**
* Classe abstrata para plugins
*
* @property string $name Nome do plugin
* 
* @property string $folter Pasta onde o plugin está localizado
* 
* @property Registry $registry Referencia Objeto Registry Global
* 
* @property Config $config Objeto config responsável por armazenar as configurações do Plugin
* 
* @property string $configFile Caminho da pasta do plugin até o arquivo de configuração
* 
* @property float $version Indica a versão do plugin. Utilize um padrão de versões de forma que a versão principal sempre indique compatibilidade. 
* Portanto a versão 1.1 deve ser compatível com a versão 1.9.2. Sempre que houver uma quebra de compatibilidade incremente a versão principal.
* Por exemplo a versão 1.9 não é compatível com a versão 2.0.
* 
* @property array $compatibleWith Indica um array de versões do Magic que este plugin é compatível.
*/
class AbstractPlugin
{
	protected $name;
	protected $folder;
	protected $registry;
	protected $config;
	protected $configFile="config.json";
	protected $version=false;
	protected $compatibleWith=false;
	final function __construct($name,$folder,Registry $registry)
	{
		$this->registry = $registry;
		$this->setName($name);
		$this->setFolder($folder);
		$this->loadConfig();
		if (!$this->getVersion()) {
			throw new LogicException("Plugin must have a version associate, so please assign a value to {$name}Plugin::version .", 1);
		}
		if (!$this->getCompatibleWith() or !is_array($this->getCompatibleWith())) {
			throw new LogicException("Plugin must have a list of versions of magic that it is compatible with, so please assign an array of values to {$name}Plugin::compatibleWith .", 1);
		}
		$this->init();
	}
	public function getVersion(){
		return $this->version;
	}
	public function getCompatibleWith(){
		return $this->compatibleWith;
	}
	public function loadConfig(){
		$configFile = $this->getFolder()."/".$this->getConfigFile();
		$config = new Config();
		if (file_exists($configFile)) {
			$config->load($configFile);
		}
		$this->config = $config; 
		return $this->config;
	}
	public function getConfigFile(){
		return $this->configFile;
	}
	public function setConfigFile($file){
		$this->configFile = $file;
	}
	public function init(){

	}
	public function setName($name){
		$this->name = $name;
	}
	public function getName(){
		return $this->name;
	}

    public function getFolder()
    {
        return $this->folder;
    }

    public function setFolder($folder)
    {
        $this->folder = $folder;

        return $this;
    }

    public function getJs($file){
    	$script = new PluginJs($file,$this->getName());
    	return $script;
    }

    public function getCss($file){
    	return new PluginCss($file,$this->getName());
    }

    public function registerHooks(){
    	$hooks = glob($this->folder."/Hooks/*");
    	$currentClass = get_class($this);
        $refl = new \ReflectionClass($currentClass);
        $namespace = $refl->getNamespaceName();
		foreach ($hooks as $hook) {
			$class = $namespace."\\Hooks\\".str_replace(".php","",basename($hook));
			$hook = new $class($this->registry);	
			$hook->register();
		}
    }
    public function __get($key) {
		return $this->registry->get($key);
	}
}
?>