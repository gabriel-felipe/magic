<?php 
namespace Magic\Engine\Sanitizer;
class SlugSanitizer extends AbstractSanitizer {
	
	public function sanitize($str){
		$a = array('À','Á','Â','Ã','Ä','Å','à','á','â','ã','ä','å','á');
		$e = array('È','É','Ê','Ë','è','é','ê','ë');
		$c = array('Ç', 'ç');
		$i = array('Ì','Í','Î','Ï','ì','í','î','ï');
		$n = array('Ñ', 'ñ');
		$o = array('Ò','Ó','Ô','Õ','Ö','ò','ó','ô','õ','ö');
		$u = array('Ù','Ú','Û','Ü','ù','ú','û','ü');
		$y = array('Ý', 'ý','ÿ');
		$all = array('a' => $a, 'e' => $e, 'c' => $c, 'i' => $i, 'n' => $n, 'o' => $o, 'u' => $u, 'y' => $y );	
		$var = strtolower($str);
	    foreach($all as $k=>$tmp){
			foreach($tmp as $l){
			${$k}[] = htmlentities($l,ENT_COMPAT,'UTF-8');
			$var = str_replace(${$k},$k, $var);
			}
		}
		$var = preg_replace("/[^A-z-0-9]+/","-", $var);
		
		return $var;
	}

}
?>