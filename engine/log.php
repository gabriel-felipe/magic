<?php 
/**
* 
*/
class log
{
    static $errorFile = path_log;
    
    static function getErrorFile(){
        return path_log."/errors.txt";
    }
    static function error($msg,$level='error'){
        $errorfile = self::getErrorFile();
        $file = fopen($errorfile,"a+");
        $tmpmsg = date("d/m/Y h:i:s |:| ");
        $msg = str_replace("\n"," -newline- ",$msg);
        $tmpmsg .= $level." |:| ".$msg."\n";
        fwrite($file, $tmpmsg);
        fclose($file);
    }

}
?>