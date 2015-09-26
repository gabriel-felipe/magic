<?php
Namespace Publico;
class Init {
	public static function run($scope){
		\Magic\Engine\Plugin\Loader::register("MagicCompiladoresDefault");
		\Magic\Engine\Plugin\Loader::register("MagicBrowserIdentify");
		\Magic\Engine\Plugin\Loader::register("EQuery");
		\Magic\Engine\Plugin\Loader::register("MAjax");
		\Magic\Engine\Plugin\Loader::register("MagicDefaultHtml");
		\Magic\Engine\Plugin\Loader::register("MagicDefaultViewCompilador");
		\Magic\Engine\Plugin\Loader::register("CSSAutoLoad");
		\Magic\Engine\Plugin\Loader::register("reloadOnSave");
	}
}