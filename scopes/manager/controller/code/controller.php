<?php 
    /**
    * Controller responsável pelas funções relacionadas a gerenciar controllers.
    */
    class ControllerCodeController extends ManagerController
    {
        
        public function index(){
            $this->template = "lightbox/addController";
            $this->load->model("Scopes");
            $this->data['scopes'] = $this->MScopes->getScopes();
            echo $this->json->success($this->get_content());
        }
        public function send(){
            $name = data::post("name",'special_chars');
            $methods = data::post("methods",'special_chars');
            $scope  = data::post("scope",'special_chars');
            if($name and $methods and $scope){
                $this->load->model("Scopes");
                $this->load->model("Code",false,array($this->MScopes));
                try {
                    $res = $this->MCode->addController($name,$methods,$scope);
                    echo $this->json->success("Controller criado com sucesso");
                } catch (Exception $e) {
                    echo $this->json->fail($e->getMessage());
                }
                
            } else {
                echo $this->json->fail("Validação falhou =/");
            }
        }
    }
?>