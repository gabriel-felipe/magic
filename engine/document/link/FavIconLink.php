<?php 
/**
 * This is a file
 * @package MagicDocument\Link\
 **/


/**
* Classe base para todas as classes de css's.
* 
* @property  $media overrided to all
* 
* @property  $rel overrided to icon
* 
* @property $type overrided to text/css
* 
*/
class FavIconLink extends LinkAbstract
{
	protected $media='all',$rel='icon',$type="image/png",$shouldCompile=0;
	
}
?>