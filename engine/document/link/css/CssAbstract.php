<?php 
/**
 * This is a file
 * @package MagicDocument\Link\Css
 **/


/**
* Classe base para todas as classes de css's.
* 
* @property  $media overrided to all
* 
* @property  $rel overrided to stylesheet
* 
* @property $type overrided to text/css
* 
* @property array $data array de variáveis acessíveis neste css.
* 
*/
abstract class CssAbstract extends LinkAbstract
{
	protected $media='all',$rel='stylesheet',$type='text/css';
	protected $data = array();
	protected $registry;
	function __construct($file){
		global $registry;
		$this->registry = $registry;
		$this->path = $file;
		$this->data['version'] = $this->browser->Version;
		$this->data['MajorVer'] = $this->browser->MajorVer;
		$this->data['MinorVer'] = $this->browser->MinorVer;
		$this->data['browser'] = $this->browser->Browser;
		$this->data['isTablet'] = $this->mobileDetect->isTablet();
		$this->data['isMobile'] = $this->mobileDetect->isMobile();
	}

	/**
	 * Compila esse css parseando os atalhos php e as variáveis contidas em $this->data;
	 * @return string Css compilado no formato de string.
	 */
	public function compile(){
		if (!$this->getIsLocal()) {
			throw new Exception("Can't compile remote css: ".$this->getAbsPath(), 1);
			return null;
		}
		//Pega o conteúdo de todos os css locais em um só.
		$content = $this->getContent();
		//Quebra ele um array de linhas
		$linhas = explode("\n",str_replace("\n\r", "\n", $content));
		//Procura uma série de padrões e convenções e faz as alterações.
		foreach($linhas as $l=>$rule){
			if(preg_match("/([^\s]+)@eq\(([^)]+)\)/",$rule,$match)){
				$expression = str_replace(" ","_",strtolower($match[2]));
				$element = $match[1];
				$linhas[$l] = str_replace($match[0],"$element.$expression",$linhas[$l]);
				$equery[] = array($element,$match[2]);
			}
			if(preg_match("/^[\s]*if(.+):$/",$rule,$match)){
				$linhas[$l] = str_replace($match[0], "<?php if(".$match[1].") { ?>",$rule);	
			}
			if(preg_match("/^[ \t\s]*end[ \t\s]*$/",$rule,$match)){
				$linhas[$l] = str_replace("end", "<?php } ?>",$linhas[$l]);
			}
			if(preg_match("/else:/",$rule,$match)){
				$linhas[$l] = str_replace("else:", "<?php } else { ?>",$linhas[$l]);
			}
			if(preg_match("/elseif  *(.+) *:/",$rule,$match)){
				$linhas[$l] = str_replace($match[0], "<?php } elseif(".$match[1].") { ?>",$rule);
			}
		}
		//Junta todas as linhas numa única string novamente
		$content = implode("\n",$linhas);
		ob_start();
		eval("?> ".$content. "<?php ");
        $content = ob_get_clean();
		return $content;
	}

	public function __get($name){
		return (isset($this->data[$name])) ? $this->data[$name] : $this->registry->get($name);
	}

	public function __set($name,$value){
		$this->data[$name] = $value;
	}
	
}
?>