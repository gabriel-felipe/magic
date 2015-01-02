<?php 
/**
* Plugin para identificar o browser no magic e nos css's / js's
*/
class MagicBrowserIdentifyPlugin extends AbstractPlugin
{
	protected $version = 1.0;
	protected $compatibleWith = array("1");
	function init()
	{
		require_once("Mobile_Detect.php");
		require_once("phpbrowsercap.php");
		
		$browser = new phpbrowscap\Browscap($this->getFolder()."/cache");
		$browser = $browser->getBrowser();
		$this->registry->set('browser',$browser);
		$mobileDetect = new Mobile_Detect();
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