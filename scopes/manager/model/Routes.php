<?php 
 /**
 * 
 */
 class Routes
 {
     
     function getRawRoutes(){
        $path = realpath(path_controllers);

        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
        $Regex = new RegexIterator($objects, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

        foreach($objects as $name => $object){
            if(preg_match('/^.+\.php$/i', $name)){
                $files[] = str_replace(path_controllers."/","",$name);
            }
        }
        $allRoutes = array();
        foreach($files as $file){
            require_once(path_controllers."/".$file);
            $class = str_replace("/","",$file);
            $class = str_replace(".php","",$class);
            $class = "Controller".$class;
            $f = new ReflectionClass($class);
            $methods = array();
            foreach ($f->getMethods() as $m) {
                if (strtolower($m->class) == strtolower($class)) {
                    $methods[] = ($m->name == "index") ? "" : $m->name;
                }
            }

            $routes = array();
            $route = str_replace("/","_",$file);
            $route = str_replace(".php","",$route);
            foreach($methods as $method){
                if($method === ""){
                    $routes[] = $route;
                } else {
                    $routes[] = $route."_$method";
                }
            }

            $allRoutes[$file] = $routes;
            
        }
        return $allRoutes;
     }
 }
?>