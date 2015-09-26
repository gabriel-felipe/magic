<?php 
namespace Magic\Engine\Autoloader;

interface InterfaceAutoloader {
	public function autoload($class);
	public function match($class);
}
