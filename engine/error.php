<?php
    function error_handler($level, $message, $file, $line, $context) {
        //Handle user errors, warnings, and notices ourself
        log::error($message,"PHP Error");
        if($level === E_USER_ERROR || $level === E_USER_WARNING || $level === E_USER_NOTICE) {
            echo '<strong>Error:</strong> '.$message."\n";
            return(true); //And prevent the PHP error handler from continuing
        }
        return(false); //Otherwise, use PHP's error handler
    }

    function trigger_my_error($message, $level,$fileInfo=false) {
        //Get the caller of the calling function and details about it
        if($fileInfo){
            $callee = $fileInfo;
        } else {
            $callee = current(debug_backtrace());    
        }
        //Trigger appropriate error
        trigger_error($message.' in <strong>'.$callee['file'].'</strong> on line <strong>'.$callee['line'].'</strong>', $level);
    }

    //Use our custom handler
    set_error_handler('error_handler');
    class debug
    {
        static function warning($msg,$fileInfo = false){
            if(!$fileInfo){
                $fileInfo = next(debug_backtrace());
            }
            trigger_my_error($msg,E_USER_WARNING,$fileInfo);
        }
    }
?>