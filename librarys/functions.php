<?php
header('Content-Type: text/html; charset=utf-8');
//Serve para deixar uma string sem caracteres especiais
function str_clean($var){
	$a = Array('À','Á','Â','Ã','Ä','Å','à','á','â','ã','ä','å');
	$e = Array('È','É','Ê','Ë','è','é','ê','ë');
	$c = Array('Ç', 'ç');
	$i = Array('Ì','Í','Î','Ï','ì','í','î','ï');
	$n = Array('Ñ', 'ñ');
	$o = Array('Ò','Ó','Ô','Õ','Ö','ò','ó','ô','õ','ö');
	$u = Array('Ù','Ú','Û','Ü','ù','ú','û','ü');
	$y = Array('Ý', 'ý','ÿ');
	$var = str_replace($a,"a", $var);
	$var = str_replace($e,"e", $var);
	$var = str_replace($c,"c", $var);
	$var = str_replace($i,"i", $var);
	$var = str_replace($n,"n", $var);
	$var = str_replace($o,"o", $var);
	$var = str_replace($u,"u", $var);
	$var = str_replace($y,"y", $var);
	$var = str_replace(" ","-", $var);
	$var = strtolower($var);
	return $var;
}
//Função para redirecionamento.
function redirect($url){
	echo "<meta http-equiv=\"refresh\" content=\"0; url=$url\">";
}
//Pegar o conteúdo de um include e colocar em uma variável
function get_include_contents($filename) {
	
    if (is_file($filename)) {
        ob_start();
        include $filename;
        return ob_get_clean();
    }
    return false;
}
//Criar form de select mantendo o valor atual selecionado
function editFormSelect($opcoes, $valorAtual){
	$html = "";
	foreach($opcoes as $value => $name){		
		$select = ($value == $valorAtual) ? "selected = 'selected' ":"";
		$html .= "<option value='$value' $select>$name</option>
		";
	}
	return $html;
}

/*
Similar a editFormSelect p/ radios. Atributos:
$name_radio = nome dos inputs radio
$opcoes = array associativo no formato valor => label
$valorAtual  = valor atual selecionado no radio
$template = uma string utilizada como padrão para gerar os inputs. Dentro da string use "[radio]" onde quiser que fique o input e [label] onde quiser o label.
*/
function editFormRadio($name_radio, $opcoes, $valorAtual,$template){
	$html = "";
	foreach($opcoes as $value => $name){
		
		$check = ($value == $valorAtual) ? "checked = 'checked' ":"";
		$templateb = str_replace("[radio]", "<input type='radio' name=\"$name_radio\" value=\"$value\" $check />
		", $template);
		$html .=str_replace("[label]", $name, $templateb);
	}
	return $html;
}

function str2num($str){ 
  if(strpos($str, '.') < strpos($str,',')){ 
            $str = str_replace('.','',$str); 
            $str = strtr($str,',','.');            
        } 
        else{ 
            $str = str_replace(',','',$str);            
        } 
        return (float)$str; 
} 

function do_post_request($url, $dados, $optional_headers = null)
{
     // Aqui entra o action do formulário - pra onde os dados serão enviados
		$cURL = curl_init($url);
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);

		// Iremos usar o método POST
		curl_setopt($cURL, CURLOPT_POST, true);
		// Definimos quais informações serão enviadas pelo POST (array)
		curl_setopt($cURL, CURLOPT_POSTFIELDS, $dados);

		$resultado = curl_exec($cURL);
		curl_close($cURL);
		return $resultado;
};
?>
