<?php
class validate {
	static function email($str){
	 	return preg_match("/^.+@.+\..+/",$str) ? $str : false;
	}
	static function str($str, $min=1,$max=10000){
		return (preg_match("/^(.|\s)+$/",$str) and strlen($str) >= $min and strlen($str) <= $max) ? $str : false;
	}
	static function int($str, $min=1, $max=10000){
		return (int)(preg_match("/^[\d]{".$min.",".$max."}$/",$str)) ? $str : false;	
	}
	static function float($str, $min=1, $max=10000){
		return (preg_match("/^[\d.,+-eE]{".$min.",".$max."}$/",$str)) ? $str : false;	
	}
	static function alpha_numeric_nospace($str,$min=1,$max=10000){
		return (preg_match("/^[áéíóúãẽĩõũÁÉÍÓÚÃẼĨÕŨÀàçÇA-z@-_.\d]{".$min.",".$max."}$/",$str)) ? $str : false;		
	}
	static function alpha_nospace($str,$min=1,$max=10000){
		return (preg_match("/^[áéíóúãẽĩõũÁÉÍÓÚÃẼĨÕŨÀàçÇA-z@-_.]{".$min.",".$max."}$/",$str)) ? $str : false;		
	}
	static function alpha_numeric($str,$min=1,$max=10000){
		return (preg_match("/^[áéíóúãẽĩõũÁÉÍÓÚÃẼĨÕŨÀàçÇA-z@-_.\d\s]{".$min.",".$max."}$/",$str)) ? $str : false;		
	}
	static function alpha($str,$min=1,$max=10000){
		return (preg_match("/^[áéíóúãẽĩõũÁÉÍÓÚÃẼĨÕŨÀàçÇA-z@-_.\s]{".$min.",".$max."}$/",$str)) ? $str : false;		
	}
	static function custom($str, $regex){
		return (preg_match($regex,$str)) ? $str : false;		
	}
	static function url($str){
		 return filter_var($str,FILTER_VALIDATE_URL);
	}
	static function absolute_url($str){
		if($str){

			return (strpos($str,"http://") !== false or strpos($str,"https://") !== false) ? true : false;
		} else {
			return false;
		}
	}
}
class sanitize {
	static function email($str){
		return filter_var($str,FILTER_SANITIZE_EMAIL);
	}
	static function int($str){
		return preg_replace("/[^\d]/", "",$str);
	}
	static function float($str){
		return filter_var($str, FILTER_SANITIZE_NUMBER_FLOAT);	
	}
	static function special_chars($str){
		return filter_var($str, FILTER_SANITIZE_SPECIAL_CHARS);	
	}
	static function alpha($str){
		return preg_replace("/[^áéíóúãẽĩõũÁÉÍÓÚÃẼĨÕŨÀàçÇA-z@-_.\s]/", "",$str);
	}
	static function url($str){
		return filter_var($str, FILTER_SANITIZE_URL);	
	}
	static function alpha_numeric($str){
		return preg_replace("/[^áéíóúãẽĩõũÁÉÍÓÚÃẼĨÕŨÀàçÇA-z@-_.\s\d,]/", "",$str);
	}
	static function no_special($str){
		return preg_replace("/[^A-z@-_.\d]/", "",$str);
	}
	static function no_script($str){
		return preg_replace("/(<[^>]*script.*>)/", htmlentities("$1"),$str);
	}
	static function url_protocol($str){
		if(!empty($str)){
	 		$url = filter_var($str, FILTER_SANITIZE_URL);
	 		if(!preg_match("/http|mailto|https|ssh/",$url)){
	 			$url = "http://$url";
	 		}
	 		if(!preg_match("/\./", $url)){
	 			$url = "$url.com";
	 		}
	 		return $url;
	 	} else {
	 		return '';
	 	}
	}
	static function sql($str){
		return mysqli_real_escape_string($str);
	}

	static function mssql($str){
        if ( !isset($str) or $str === "") return '';
	    if ( is_numeric($str) ) return $str;

        $non_displayables = array(
            '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
            '/%1[0-9a-f]/',             // url encoded 16-31
            '/[\x00-\x08]/',            // 00-08
            '/\x0b/',                   // 11
            '/\x0c/',                   // 12
            '/[\x0e-\x1f]/'             // 14-31
        );
        foreach ( $non_displayables as $regex )
            $str = preg_replace( $regex, '', $str );
        $str = str_replace("'", "''", $str );
        return $str;
	}
	static function no_accents_n_spaces($str){
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
	static function no_accents($str){
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
		return $var;
	}
	static function color($str){
		return preg_replace("/[^A-z,()#\d]/", "",$str);
	}
}
class data {
	static function get($value,$sanitize=false,$validate=false, $validateParams = false){
		if(isset($_GET[$value])){
			return self::cleaner($_GET[$value],$sanitize,$validate,$validateParams);	
		} else {
			return false;
		}
		
	}
	static function post($value,$sanitize=false,$validate=false, $validateParams = false){
		if(isset($_POST[$value])){
			return self::cleaner($_POST[$value],$sanitize,$validate,$validateParams);	
		} else {
			return false;
		}
	}
	static function session($value,$sanitize=false,$validate=false, $validateParams = false){
		if(isset($_SESSION[$value])){
			return self::cleaner($_SESSION[$value],$sanitize,$validate,$validateParams);	
		} else {
			return false;
		}
		
	}
	static function cookie($value,$sanitize=false,$validate=false, $validateParams = false){
		if(isset($_COOKIE[$value])){
			return self::cleaner($_COOKIE[$value],$sanitize,$validate,$validateParams);	
		} else {
			return false;
		}
		
	}
	static function cleaner($value,$sanitize=false,$validate=false,$validateParams=false){
		if(isset($value) and !empty($value)){
			if($sanitize){
				if(method_exists('sanitize', $sanitize)){
					$value = sanitize::$sanitize($value);
					if($validate){
						if(method_exists('validate', $validate)){
							$params = array($value);
							if(is_array($validateParams)){
								$params = array_merge($params, $validateParams);
							}
							$valida = call_user_func_array(array("validate", $validate), $params);
							if(!$valida){
								return false;
							} else {
								return $value;
							}
						} else {
							$method = $validate[0];
							throw new Exception("Erro ao validar $att, método de validação $method não existe =(", 1);
						}
					} else{
						return $value;
					}
				} else {	
					throw new Exception("Error method $sanitize doesn't exist into sanitizer class", 1);
				}
			} else {
				return $value;
			}
		} else {
			return false;
		}
	}
}
/*Processo Padrão:
$email = $_POST['email'];
limpa os dados do email
if(validação de email){
	envia email
}
--------------------------
Processo Com a Classe
$email = data::post('email','email','email');
$nome = data::post('nome','alpha','alpha');
$telefone = data::post('telefone','int','int',array(8,11));


if($email){
	envia email
}
*/
?>