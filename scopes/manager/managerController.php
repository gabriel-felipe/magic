<?php 
/**
* 
*/
class ManagerController extends Controller
{
    
    public function basicLayoutTasks(){
        
        $this->children = array("common/layout/header","common/layout/footer");
    }
}
?>