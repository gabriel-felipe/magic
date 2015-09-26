<?php 
namespace Magic\Engine\Mvc\View;
use Magic\Engine\Mvc\View\Compiladores\InterfaceViewCompilador;
use Magic\Engine\Mvc\View\Compiladores\ViewCompilador;
use Magic\Engine\Mvc\View\Compiladores\AbstractViewCompiladorDecorator;
use \Exception;
/**
 * This is a file
 * @package MVC\View
 **/

/**
* Classe de origem de todas as views
* 
* @property protected $path é o caminho para aquela view. 
*
* @property string $rootPath onde procurar a view.
*  
* Deve ser fornecido um valor a partir da raiz, algo como: "/common/css/" 
*/
Abstract class AbstractView
{
	protected $path=false;
	protected $ext="tpl";
	protected $rootPath="/";
	protected $compilador=false;
	protected $data = array();
    protected $registry;

    function __construct($file=false){
        global $registry;
        $this->registry = $registry;
        if ($file) {
            $this->setPath($file);
        }
    }

  	
    public function __set($name,$value){
        $this->data[$name] = $value;
    }
    public function setCompilador(InterfaceViewCompilador $compilador){
    	$compilador->setView($this);
        $this->compilador = $compilador;
    }

    public function getCompilador(){
        if (!$this->compilador) {
            $this->setCompilador(new ViewCompilador());
        }
        return $this->compilador;
    }

    public function resetCompilador(){
        $this->setCompilador(new ViewCompilador());
        return $this;
    }

    public function addCompiladorDecorator(InterfaceViewCompilador $decorator){
        $decorator->setCompilador($this->getCompilador());
        return $this->setCompilador($decorator);
    }

    /**
     * retorna o caminho para o arquivo, se for local concatena com o diretório raiz.
     */
    public function getPath(){
        return $this->rootPath.$this->path.".".$this->getExt();
    }

    /**
     * Gets the value of path prepended by path_root when asset is local, 
     * when its not, returns the path only.
     *
     * @return mixed
     */
    public function getAbsPath()
    {
    	return path_root.$this->getPath();
    	
    }

    /**
     * Gets the value of path prepended by path_base when asset is local, 
     * when its not, returns the path only.
     *
     * @return mixed
     */
    public function getRelPath()
    {
    	return path_base.$this->getPath();
    }

    /**
     * Sets the value of path.
     *
     * @param mixed $path the path
     *
     * @return self
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    
    /**
     * @return string Pega o conteúdo do asset e retorna em forma de string.
     */
    public function getContent(){
        return file_get_contents($this->getAbsPath());
    }
    public function doExist(){
        if (!is_file($this->getAbsPath())) {
            return false;
        }
        return true;
    }
    /**
     * Compila esse css parseando os atalhos php e as variáveis contidas em $this->data;
     * @return string Css compilado no formato de string.
     */
    public function render(){
        if (!$this->doExist()) {
            throw new Exception("Can't render a view that does not exist: ".$this->getAbsPath(), 1);
            return null;
        }
        //Pega o conteúdo de todos os css locais em um só.
        $content = $this->getContent();
        $content = str_replace("magic_path",dirname($this->getRelPath()),$content);
        $compilador = $this->getCompilador();
        $content = $compilador->compilarTodos($content);
        return $content;
    }


    public function getData(){
        return $this->data;
    }
    public function setData(array $data){
        $this->data = $data;
        return $this;
    }
    public function mergeData(array $data){
    	$this->data = array_merge($this->data,$data);
    }

    public function __get($name){
        return (isset($this->data[$name])) ? $this->data[$name] : $this->registry->get($name);
    }

    /**
     * @return string Pega o conteúdo do asset e retorna em forma de string.
     */
    
    public function getModDate(){
        return ($this->getIsLocal()) ? filectime($this->getAbsPath()) : 0;
    }

    /**
     * Gets the value of ext.
     *
     * @return mixed
     */
    public function getExt()
    {
        return $this->ext;
    }


    /**
     * Sets the value of ext.
     *
     * @param mixed $ext the ext
     *
     * @return self
     */
    public function setExt($ext)
    {
        $this->ext = $ext;

        return $this;
    }

    function __clone(){
        $this->compilador = clone $this->compilador;
        $this->compilador->setView($this);
    }
}
?>