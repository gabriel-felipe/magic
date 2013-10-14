<?php
    class Controllercodedbmodel extends Controller {
        public function index(){
            $this->template = "lightbox/addDbModel";
            $this->load->model("Scopes");
            $this->data['scopes'] = $this->MScopes->getScopes();
            echo $this->json->success($this->get_content());
        }
        public function send(){
            $table = data::post("table",'special_chars');
            $single = data::post("single",'special_chars');
            $plural = data::post("plural",'special_chars');
            $scope  = data::post("scope",'special_chars');
            if($single and $table and $scope and $plural){
                $this->load->model("Scopes");
                $this->load->model("Code",false,array($this->MScopes));
                try {
                    $res = $this->MCode->addDbModel($table,$single,$plural,$scope);
                    echo $this->json->success("DbModel criado com sucesso");
                } catch (Exception $e) {
                    echo $this->json->fail($e->getMessage());
                }
                
            } else {
                echo $this->json->fail("Validação falhou =/");
            }
        }

    }
?>