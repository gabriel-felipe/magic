<?php

	ini_set('display_errors',1); 
 	error_reporting(E_ALL);
 	ini_set('html_errors', 'On');
	require_once('librarys/data-cleaner.php');

	$ns = data::get('ns','url');
	if($ns){
		$namespace = $ns;
	} else {
		$namespace = "admin";
	} //Se tiver namespace definido na url

	$theme = "default";

	require_once('init.php');	

	
	$registry = new registry;
	$loader = new loader($registry);
	$registry->set('load',$loader);
	$loader->library('login');

	$url = new url;

	$registry->set('url',$url);
	require_once(path_datamgr."/bff-dbconnect.php");
	$db = new bffdbconnect;
	
	$writtableDirs = array(path_cache,path_cache."/images",path_uploads);
	
?>
<style>
.ok {background: #dfd;}
.nok {background: #fdd;}
</style>
<?php if($db->connect()) { ?>
<div class='ok'>
<h1> Db STATUS - Ok!</h1>
</div>
<?php } else { ?>
<div class='nok'>
	<h1> Db STATUS - Not Ok!</h1>
	<h2> Check Info</h2>
	<table>
		<tr>
			<td>Tipo</td>
			<td><?php echo db_driver?></td>
		</tr>
		<tr>
			<td>Host</td>
			<td><?php echo db_host?></td>
		</tr>
		<tr>
			<td>Db User</td>
			<td><?php echo db_user?></td>
		</tr>
		<tr>
			<td>Db Name</td>
			<td><?php echo db_name?></td>
		</tr>
		<tr>
			<td>Db Password</td>
			<td><?php echo db_password?></td>
		</tr>
	</table>
</div>
<?php } ?>


<h1>Write Permissions</h1>

<table border='1'>
	<tr>
		<td>Path</td>
		<td>Should Be</td>
	</tr>
<?php 

function getDirectory( $path = '.', $level = 0){ 
	global $writtableDirs;
    $ignore = array( 'cgi-bin', '.', '..' ); 
    // Directories to ignore when listing output. Many hosts 
    // will deny PHP access to the cgi-bin. 

    $dh = @opendir( $path ); 
    // Open the directory to the handle $dh 
     
    while( false !== ( $file = readdir( $dh ) ) ){ 
    // Loop through the directory 
     
        if( !in_array( $file, $ignore ) ){ 
        // Check that this file is not to be ignored 
             
            $spaces = str_repeat( '&nbsp;', ( $level * 4 ) ); 
            // Just to add spacing to the list, to better 
            // show the directory tree. 
             $shouldWrite = (in_array($path."/".$file, $writtableDirs)) ? true : false;
         	if($shouldWrite) {

         		$class = (is_writable($path."/".$file)) ? "ok" : "nok";
         		$shouldBe = "Writtable";
                echo "<tr class='$class'><td>$path/$file</td><td>$shouldBe<td></tr>"; 

         	}
            if( is_dir( "$path/$file" ) ){ 
            // Its a directory, so we need to keep reading down... 

                getDirectory( "$path/$file", ($level+1) ); 
                // Re-call this same function but on a new directory. 
                // this is what makes function recursive. 
             
            }
         
        } 
     
    } 
     
    closedir( $dh ); 
    // Close the directory handle 

} 

getDirectory(path_root);

?>
</table>
