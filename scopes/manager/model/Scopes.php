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
            $scope = strtolower($scope);
            if(is_dir(path_scopes."/".$scope)){
                throw new Exception("Scope already exists, aborting", 1);
                return false;
            } else {
                $dirs = array(
                    $scope => array(
                        "controller",
                        "model",
                        "language" => array(
                            "pt-br"
                        ),
                        "views" => array(
                            "js",
                            "default" => array(
                                "css",
                                "template"
                            )
                        )
                    )
                );
                
                $scopeController = "
<?php
class ".$scope."Controller extends Controller
{

}
?>
";
                fileManager::recursiveMkDir($dirs,path_scopes);

                $fileName = path_scopes."/$scope/".$scope."Controller.php";
                $handler = fopen($fileName,"w+");

                fwrite($handler, $scopeController);
                fclose($handler);

                $scopeInit = "
<?php
require(\"".$scope."Controller.php\");
?>
";
                 $fileName = path_scopes."/$scope/init.php";
                $handler = fopen($fileName,"w+");

                fwrite($handler, $scopeInit);
                fclose($handler);
            }
        }
    }
?>