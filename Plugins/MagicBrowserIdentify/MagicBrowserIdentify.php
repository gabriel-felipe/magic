<?php 
namespace Magic\Plugins\MagicBrowserIdentify;
use Magic\Engine\Plugin\AbstractPlugin;
/**
* Plugin para identificar o browser no magic e nos css's / js's
*/
class MagicBrowserIdentify extends AbstractPlugin
{
	protected $version = 1.0;
	protected $compatibleWith = array("1");
	function init()
	{
		
		$browser = new Browscap($this->getFolder()."/cache");
		$browser = $browser->getBrowser();
		$this->registry->set('browser',$browser);
		$mobileDetect = new MobileDetect();
		$this->registry->set("mobileDetect",$mobileDetect);
		$this->LinkManager->version = $this->browser->Version;
        $this->LinkManager->MajorVer = $this->browser->MajorVer;
        $this->LinkManager->MinorVer = $this->browser->MinorVer;
        $this->LinkManager->browser = $this->browser->Browser;
        $this->LinkManager->isTablet = $this->mobileDetect->isTablet();
        $this->LinkManager->isMobile = $this->mobileDetect->isMobile();
	}
}
?>