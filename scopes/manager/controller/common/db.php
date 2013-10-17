<?php 
    /**
    * 
    */
    class ControllerCommonDb extends ManagerController
    {
        
        public function update()
        {
            $configFile = path_root."/config/db.json";
            if(is_writable($configFile)){
                if(array_key_exists("db", $_POST)){
                    $db = $_POST['db'];
                    foreach($db as &$info){
                        $info = sanitize::special_chars($info);
                    }
                    $handler = fopen($configFile,"w+");
                    fwrite($handler, json_encode($db));
                    fclose($handler);
                    echo $this->json->success("Configurações salvas com sucesso!");
                } else {
                    echo $this->json->fail("Parâmetros não encontrados.");     
                }
            } else {
               echo $this->json->fail("Permissão negada.");
            }
        }
    }
?>