<?php 
namespace Magic\Engine\Document;
use Magic\Engine\Compilador\Compilador;
use Magic\Engine\Compilador\AbstractCompiladorDecorator;
use Magic\Engine\Compilador\InterfaceCompilador;
use Magic\Engine\Hooks\HookChainManager;
use Magic\Engine\Hooks\AbstractHook;
use \Exception;
/**
* Classe responsável por gerenciar vários assets
*/

abstract class AbstractAssetManager {
	
	protected $compiladorUnidade;
	protected $compiladorGrupo;
	protected $compiladorMinificacao;
	protected $assetType;
	protected $assets = array();
	protected $cacheFolder;
	protected $globalData = array();
	protected $hooks = false;
	public function __construct(){
		global $registry;
		$this->cacheFolder = path_cache."/document/asset/".$this->assetType."/";
		$this->compiladorUnidade = new Compilador;
		$this->compiladorGrupo = new Compilador;
		$this->compiladorMinificacao = new Compilador;
		$this->hooks = new HookChainManager;
	}
	public function getGlobalData(){
		return $this->globalData;
	}
	public function setGlobalData(array $data){
		$this->globalData = $data;
	}
	public function addAsset(AbstractAsset $asset){
		if (!$asset->doExist()) {
			throw new Exception("Trying to add {$this->assetType} asset that does not exist: ".$asset->getAbsPath(), 1);
			
		}
		$this->hooks->callChain("beforeAddAsset",array($asset));
		$asset->mergeData($this->getGlobalData());
		$this->assets[md5($asset->getAbsPath())] = $asset;
		$this->hooks->callChain("afterAddAsset",array($asset));
	}

	public function setCompiladorUnidade(InterfaceCompilador $compilador){
        $this->compiladorUnidade = $compilador;
        foreach ($this->assets as $asset) {
			$asset->setCompilador($this->compiladorUnidade());
		}
    }

    public function getCompiladorUnidade(){
        if (!$this->compiladorUnidade) {
            $this->compiladorUnidade = new Compilador();
        }
        return $this->compiladorUnidade;
    }
    public function addCompiladorUnidadeDecorator(AbstractCompiladorDecorator $decorator){
        $decorator->setCompilador($this->getCompiladorUnidade());
        return $this->setCompiladorUnidade($decorator);
    }

    public function setCompiladorGrupo(InterfaceCompilador $compilador){
        $this->compiladorGrupo = $compilador;
    }

    public function getCompiladorGrupo(){
        if (!$this->compiladorGrupo) {
            $this->compiladorGrupo = new Compilador();
        }
        return $this->compiladorGrupo;
    }
    public function addCompiladorGrupoDecorator(AbstractCompiladorDecorator $decorator){
        $decorator->setCompilador($this->getCompiladorGrupo());
        return $this->setCompiladorGrupo($decorator);
    }

    public function setCompiladorMinificacao(InterfaceCompilador $compilador){
        $this->compiladorMinificacao = $compilador;
    }

    public function getCompiladorMinificacao(){
        if (!$this->compiladorMinificacao) {
            $this->compiladorMinificacao = new Compilador();
        }
        return $this->compiladorMinificacao;
    }
    public function addCompiladorMinificacaoDecorator(AbstractCompiladorDecorator $decorator){
        $decorator->setCompilador($this->getCompiladorMinificacao());
        return $this->setCompiladorMinificacao($decorator);
    }
    /**
	 * Compila todos os css locais em um só usando um compilador less.
	 * @return AbstractCss retorna um objeto abstract css referindo o caminho do novo arquivo de cache gerado.
	 */
	public function compileLocalAssets(){
		$filename = $this->getCachePath();
		$result = "";
		$assetsToCompile = 0;
		foreach($this->assets as $asset){
			if ($asset->getIsLocal() and $asset->getShouldCompile()) {
				$assetsToCompile++;
			}
		}
		if ($this->shouldRegenerateCache()) {
			$this->hooks->callChain("beforeCompileAssets");
			
			foreach($this->assets as $asset){
				$asset->setCompilador($this->getCompiladorUnidade());
				
				if ($asset->getIsLocal() and $asset->getShouldCompile()) {
					
					$result .= $asset->compilar()."\n";
				}
			}
			$compiladorGrupo = $this->getCompiladorGrupo();
			$result = $this->minify($this->compiladorGrupo->compilarTodos($result));
			$file = fopen($filename, "w+");
			fwrite($file, $result);
			fclose($file);
			$this->hooks->callChain("afterCompileAssets");
		}
		if ($assetsToCompile) {
			return $this->getCacheAsset(basename($filename));
		} else {
			return false;
		}
		
	}

	public function registerHook(AbstractHook $hook, $chainName){
		$this->hooks->registerHook($hook,$chainName);
	}

	/**
	 * Retorna a timestamp de última modificação dos css
	 */
	public function getLastAssetMod(){
		$lastMod = 0;
		foreach($this->assets as $asset){
			if ($asset->getIsLocal()) {
				$modDate = $asset->getModDate();
				if ($modDate > $lastMod ) {
					$lastMod = $modDate;
				}	
			}
		}

		return $lastMod;
	}
	/**
	 * Retorna todos os asset.
	 * @param boolean $compileLocals Se true, compila todos os assets's locais em um só, do contrário traz link o asset normalmente.
	 */
	public function getAssets($compileLocals=1){
		$this->hooks->callChain("beforeGetAssets");
		$result = array();
		if (!$compileLocals) {
			foreach($this->assets as $asset){
				$result[$asset->getAbsPath()] = $asset->toString();
			}
			$this->hooks->callChain("afterGetAssets");
			return $result;
		} else {
			foreach($this->assets as $asset){
				if (!$asset->getIsLocal() or !$asset->getShouldCompile()) {
					$result[$asset->getAbsPath()] = $asset->toString();	
				}
			}
			$allCached = $this->compileLocalAssets();
			if ($allCached) {
				$result[$allCached->getAbsPath()] = $allCached->toString();
			}
			$this->hooks->callChain("afterGetAssets");	
			return $result;
		}
		
	}

	/**
	 * Checa se o cache css para os css's locais inseridos deve ser regerado.
	 */
	public function shouldRegenerateCache(){
		$cacheFile = $this->getCachePath();

		if (!is_file($cacheFile)) { //Se o arquivo não existir gerar.
			return true;
		}
		$modDate = filectime($cacheFile);

		if ($modDate < $this->getLastAssetMod()) { //Se um css foi modificado depois de o arquivo de cache

			return true;
		}
		return false; //retorna falso se nenhuma das condições acima for atingida.
	}

	function minify($content) {
		return $this->compiladorMinificacao->compilarTodos($content);
	}
	/**
	 * Retorna o nome do arquivo de cache.
	 */
	public function getCachePath(){
		$result = serialize($this->getCompiladorGrupo()).serialize($this->getCompiladorUnidade()).serialize($this->getCompiladorMinificacao()).serialize($this->getGlobalData());
		$data = array();
		foreach($this->assets as $asset){
			if ($asset->getIsLocal()) {
				$data[] = $asset->getData();
				$result .= serialize($asset->getData()).$asset->getAbsPath();
			}
		}
		$filepath = $this->cacheFolder.md5($result).".".$this->getCacheExt();
		return $filepath;
	}

	abstract function getCacheAsset($file);
	abstract function getCacheExt();
	function __set($key,$value){
		$this->globalData[$key] = $value;
		foreach ($this->assets as $asset) {
			$asset->mergeData($this->getGlobalData());
		}
	}
}
?>