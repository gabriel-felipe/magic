<?php 
/**
* 
*/
class ControllerCommonLayout extends ManagerController
{
    
    public function header(){
        $this->template ='common/header';
        echo $this->get_content();
    }
}
?>