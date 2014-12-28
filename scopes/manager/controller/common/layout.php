<?php 
/**
* 
*/
class ControllerCommonLayout extends ManagerController
{
    
    public function header(){
        $this->set_template('common/header');
        $this->html->addLink(new ScopeCss('style.css'));
        $this->html->addScript(new ScopeJs("lightbox.js"));
        echo $this->get_content();
    }
    public function footer(){
        $this->set_template('common/footer');
        echo $this->get_content();
    }
}
?>