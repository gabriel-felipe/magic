<?php 

    
    class Scopes
    {
        //Retorna os escopos disponíveis
        public function getScopes(){
            $scopes = glob(path_root."/scopes/*",GLOB_ONLYDIR);
            $scopes = array_map("basename",$scopes);
            return $scopes;
        }
        public function getScopePath($scope){
            return path_scopes."/$scope";
        }
        public function addScope($scope){
            require_once(path_library."/fileManager.php");
            if(is_dir(path_scopes."/".$scope)){
                throw new Exception("Scope already exists, aborting", 1);
                return false;
            } else {
                $dirs = array(
                    $scope => array(
                        "controller",
                        "model",
                        "views" => array(
                            "js",
                            "default" => array(
                                "css",
                                "template"
                            )
                        )
                    )
                );

                fileManager::recursiveMkDir($dirs,path_scopes);
            }
        }
    }
?>