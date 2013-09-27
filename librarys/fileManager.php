<?php 
    /**
    * 
    */
    class FileManager
    {
        
        static function recursiveMkDir($dirs,$base=__FILE__){
            if($base == __FILE__){
                $base = dirname($base);
            }
            $base = rtrim($base,"/");
            if(!is_dir($base)){
                throw new Exception("Error Processing Request, base dir doesn't exists", 1);
                
            }
            if(is_array($dirs)){
                foreach($dirs as $dir=>$subdirs)
                    if(is_array($subdirs)){
                        mkdir($base."/".$dir);
                        self::recursiveMkDir($subdirs,$base."/".$dir);
                    } else {
                        mkdir($base."/".$subdirs);
                    }
            } else {
                return mkdir($dirs);
            }
        }
        static function mkFiles($files,$base=__FILE__){
            if($base == __FILE__){
                $base = dirname($base);
            }
            $base = rtrim($base,"/");
            if(!is_dir($base)){
                throw new Exception("Error Processing Request, base dir doesn't exists", 1);   
            }
            if(!is_array($files)){
                throw new Exception("Files expected to be an array.", 1);
            } else {
                foreach($files as $file => $content){
                    if(!file_exists($base."/".$file)){
                        $handle = fopen($file,"w+");
                        fwrite($handle, $content);
                        fclose($handle);
                    } else {
                        $backTrace = debug_backtrace();
                        $backTrace = next($debug_backtrace);
                        debug::warning("File already exists, so it wasn't created", $backTrace);
                    }
                }
            }
        }
    }
?>