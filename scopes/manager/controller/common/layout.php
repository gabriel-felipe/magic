<?php 
/**
* 
*/
class ControllerCommonLayout extends ManagerController
{
    
    public function header(){
        $this->template ='common/header';
        $this->html->add_css_linked('style.css');
        echo $this->get_content();
    }
}
?>