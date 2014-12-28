<?php 
/**
 * This is a file
 * @package MagicDocument
 **/

/**
* Classe de origem de todos os links e scripts.
* 
* @property protected $path é o caminho para aquele asset. 
* No caso de um asset local deve ser utilizado o caminho absoluto a partir da raiz.
* Já no caso de um asset remoto o endereço http deve ser fornecido.
* 
* @property protected $isLocal propriedade booleana indicativa se o asset é local ou não.
*
* @property string $rootPath onde procurar os arquivos css's. 
* Deve ser fornecido um valor a partir da raiz, algo como: "/common/css/" 
*/
Abstract class AbstractAsset
{
	protected $path,$isLocal=1,$rootPath;
	
    /**
     * retorna o caminho para o arquivo, se for local concatena com o diretório raiz.
     */
    public function getPath(){
        if ($this->getIsLocal()) {
            return $this->rootPath.$this->path;
        } else {
            return $this->path;
        }
    }

    /**
     * Gets the value of path prepended by path_root when asset is local, 
     * when its not, returns the path only.
     *
     * @return mixed
     */
    public function getAbsPath()
    {
    	if ($this->getIsLocal()) {
    		return path_root.$this->getPath();
    	} else {
    		return $this->getPath();
    	}   
    }

    /**
     * Gets the value of path prepended by path_base when asset is local, 
     * when its not, returns the path only.
     *
     * @return mixed
     */
    public function getRelPath()
    {
    	if ($this->getIsLocal()) {
    		return path_base.$this->getPath();
    	} else {
    		return $this->getPath();
    	}   
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
     * Gets the value of isLocal.
     *
     * @return mixed
     */
    public function getIsLocal()
    {
        return $this->isLocal;
    }

    /**
     * Sets the value of isLocal.
     *
     * @param mixed $isLocal the is local
     *
     * @return self
     */
    public function setIsLocal($isLocal)
    {
        $this->isLocal = $isLocal;

        return $this;
    }
    /**
     * @return string Pega o conteúdo do asset e retorna em forma de string.
     */
    public function getContent(){
        return file_get_contents($this->getAbsPath());
    }
    /**
     * @return string Pega o conteúdo do asset e retorna em forma de string.
     */
    
    public function getModDate(){
        return ($this->getIsLocal()) ? filectime($this->getAbsPath()) : 0;
    }
}
?>