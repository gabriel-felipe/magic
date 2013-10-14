<?php 
/**
* 
*/
class ControllerCommonLayout extends ManagerController
{
    
    public function header(){
        $this->template ='common/header';
        $this->html->add_css_linked('style.css');
        $this->html->add_js_linked("lightbox.js");
        echo $this->get_content();
    }
    public function footer(){
        $this->template ='common/footer';
        echo $this->get_content();
    }
}
?>