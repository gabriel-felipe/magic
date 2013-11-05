<?php 
    function recursiveGlob($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
       
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
        {
            $files = array_merge($files, recursiveGlob($dir.'/'.basename($pattern), $flags));
        }
       
        return $files;
    }
?>