<?php 
namespace Magic\Engine\Document\Link\Css;
use Magic\Engine\Document\Link\LinkAbstract;
/**
 * This is a file
 * @package MagicDocument\Link\Css
 **/


/**
* Classe base para todas as classes de css's.
* 
* @property  $media overrided to all
* 
* @property  $rel overrided to stylesheet
* 
* @property $type overrided to text/css
* 
*/
abstract class CssAbstract extends LinkAbstract
{
	protected $media='all',$rel='stylesheet',$type='text/css';
	
}
?>