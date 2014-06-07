<?php 
/**
* Classe responsável por gerar códigos
*/
class Code
{
    private $scopesModel,$scopes;
    function __construct(Scopes $scopes)
    {
        $this->scopesModel = $scopes;
        $this->scopes = $scopes->getScopes();
    }
    function addController($name,$methods,$scope){
        $name = str_replace(" ","",$name);
        $names = explode("/",$name);
        $name = array_pop($names);

        $folder = implode("/",$names);
        if(in_array($scope, $this->scopes)){
            $dir = $this->scopesModel->getScopePath($scope);
            $dir .= "/controller/".$folder;
            if(!is_dir($dir)){
                mkdir($dir,0775,true);
            }
            $fileName = $dir."/$name.php";
            if(is_file($fileName)){
                throw new Exception("Controller Já existe", 1);
            } else {
                $className = "Controller".str_replace("/","",$folder);
                $className .= $name;
                $file = "<?php
    class $className extends ".$scope."Controller {
";
                $methods = explode(",",$methods);
                foreach ($methods as $method) {
                    $file .= "
        public function $method(){

        }
";
                }
                $file .= "
    }
?>";
                $handler = fopen($fileName,"w+");

                fwrite($handler, $file);
                fclose($handler);
                return "ok";    
            }
            
        } else {
            throw new Exception("Escopo \"$scope\" não existe", 1);
            
        }
    }
    function addDbModel($table,$single,$plural,$scope){
        $name = sanitize::no_accents_n_spaces($single);
        if(in_array($scope, $this->scopes)){
            $dir  = $this->scopesModel->getScopePath($scope);
            $dir .= "/model";
            $fileName = $dir."/$name.php";
            if(is_file($fileName)){
                throw new Exception("DbModel Já existe", 1);
            } else {
                
                
                $file = "<?php
    class $single extends dbModel
    {
        public function __construct(\$fields=false,\$query=false,\$queryParams=false)
        {
            parent::__construct(\"$table\", \$fields,\$query,\$queryParams); 
        }
    }
    class $plural extends dbModelPlural
    {
        public \$single = \"$single\";
        public function __construct(\$fields=false,\$query=false,\$queryParams=false,\$plural=array(),\$page=1,\$qtnbypage=9999999999)
        {                   
            parent::__construct(\"$table\", \$fields,\$query,\$queryParams,\$plural,\$page,\$qtnbypage);
        }   
    }
?>
";
                $handler = fopen($fileName,"w+");

                fwrite($handler, $file);
                fclose($handler);
                return "ok";    
            }
            
        } else {
            throw new Exception("Escopo \"$scope\" não existe", 1);
            
        }
    }
}
?>