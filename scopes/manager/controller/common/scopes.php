<?php
    class Controllercommonscopes extends Controller {

        public function novo(){
            $this->load->model("Scopes");
            $scope  = data::post("scopo",'special_chars');
            try {
                $this->MScopes->addScope($scope);
                echo $this->json->success("Scopo Criado com Sucesso");
            } catch (Exception $e) {
                echo $this->json->fail($e->getMessage());
            }

        }

        public function deleta(){

        }

    }
?>