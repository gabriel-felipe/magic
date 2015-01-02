<?php 
namespace Magic\Engine\Scope\Assets;
use Magic\Engine\Mvc\View\AbstractView;
final class ThemeView extends AbstractView {
	protected $scope;
	public function __construct($file,$scope){
		$this->scope = $scope;
		parent::__construct($file);
	}
	public function setPath($path,$checkExistence=1){
		$c = 0;
		foreach ($this->scope->getThemes() as $theme) {
			$c++;
			$this->rootPath = "/scopes/".$this->scope->getName()."/views/".$theme."/template/";
			$this->path = $path;
			if ($this->doExist()) {
				break;
			}
			if ($checkExistence) {
				
				if (!$this->doExist() and $c == count($this->scope->getThemes())) {
					throw new Exception("View $path não foi encontrada em nenhum dos temas (".implode(",",$this->scope->getThemes()).") do escopo {$this->scope->getName()}", 1);
					
				}
				# code...
			}
		}
		return true;
	}
}
?>