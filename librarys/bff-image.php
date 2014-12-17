<?php
//MANIPULACAO DE IMAGENS
class bffimage{
//--------------------------------------------**Efeitos de cor!**------------------------------------------------------//
	protected $img;
	protected $path;
	public function __construct($path=false){
		if($path){
			$this->path = $path;
			$this->img = $this->novaimg($path);
		}
	}
	public function pb($img){
		for($y = 0; $y<imagesy($img); $y++){
			for($x =0; $x<imagesx($img); $x++){
			$cor = imagecolorat($img, $x, $y);
			$r = ($cor >> 16) & 0xFF;
			$g = ($cor >> 8) & 0xFF;
			$b = $cor & 0xFF;
			$escalaCinza = ($r + $g + $b)/3;
				if($escalaCinza >=128){
				$corFinal = imagecolorallocate($img, 255, 255, 255);
				} else {
				$corFinal = imagecolorallocate($img, 0, 0, 0);
				}
			imagesetpixel($img, $x,$y, $corFinal);
			}
		}
	}
	
		
	public function brilho_contraste($img, $brilho=50, $contraste=10){
	$contraste = $contraste * -1;
	imagefilter($img, IMG_FILTER_BRIGHTNESS, $brilho);
	imagefilter($img, IMG_FILTER_CONTRAST, $contraste);
	}
	
	public function noiseReduce($img, $configuracoesUser){
		$default = array(
		"xinicial" => 0,
		"xfinal" => imagesx($img),
		"yinicial" => 0,
		"yfinal" => imagesy($img),
		"margem" => 2,
		"resistencia" => 3
		);
		if(is_array($configuracoesUser)){
		$default = array_merge($default, $configuracoesUser);
		}
		$xinicial = $default['xinicial'];
		$xfinal = $default['xfinal'];
		$yinicial = $default['yinicial'];
		$yfinal = $default['yfinal'];
		$margem = $default['margem'];
		$resistencia = $default['resistencia'];
		$img2 = imagecreatetruecolor(imagesx($img),imagesy($img));
		

		for($y = $yinicial; $y<$yfinal; $y++){
			for($x = $xinicial; $x<$xfinal; $x++){
			
				$qtnPixel = 0;
				$r = 0;
				$b = 0;
				$g = 0;
				$xini = ($x - $margem >= 0) ? $x - $margem : 0;
				$xfim = ($x + $margem <= imagesx($img)) ? $x + $margem : imagesx($img);
				$yini = ($y - $margem >= 0) ? $y - $margem : 0;
				$yfim = ($y + $margem <= imagesy($img)) ? $y + $margem : imagesy($img);
					for($xv = $xini; $xv<=$xfim; $xv++){
						for($yv = $yini; $yv <= $yfim; $yv++){
						$qtnPixel++;
						$cor = imagecolorat($img, $xv, $yv);
						$r += ($cor >> 16) & 0xFF;
						$g += ($cor >> 8) & 0xFF;
						$b += $cor & 0xFF;
						}
					}
					if($resistencia >= 0){
						for($re = 0; $re<$resistencia; $re++){
						$qtnPixel++;
						$cor = imagecolorat($img, $x, $y);
						$r += ($cor >> 16) & 0xFF;
						$g += ($cor >> 8) & 0xFF;
						$b += $cor & 0xFF;
						}
					} else {
					$qtnPixel--;
					$cor = imagecolorat($img, $x, $y);
					$r -= ($cor >> 16) & 0xFF;
					$g -= ($cor >> 8) & 0xFF;
					$b -= $cor & 0xFF;
					}
				$r = $r/$qtnPixel;
				$g = $g/$qtnPixel;
				$b = $b/$qtnPixel;
				$corFinal = imagecolorallocate($img, $r, $g, $b);
				imagesetpixel($img2, $x,$y, $corFinal);
				}
			}
		return $img2;
		}	
	
	public function efeitoCamada($efeito, $img, $destino, $posx, $posy){
	$img2 = imagecreatetruecolor(imagesx($img), imagesy($img));
	imageSaveAlpha($img2, true);
	imageAlphaBlending($img2, false);
	$transparent = imageColorAllocateAlpha($img2, 0, 0, 0, 127);
	imagefilledrectangle($img2, 0, 0, imagesx($img2), imagesx($img2), $transparent);
	imageAlphaBlending($img2, true);
	$img3 = imagecreatetruecolor(imagesx($img), imagesy($img));
	imagecopy($img3, $img, 0, 0, 0, 0, imagesx($img), imagesy($img));
	for($y = 0; $y<imagesy($img2); $y++){
			for($x =0; $x<imagesx($img2); $x++){
			$cor = imagecolorat($img3, $x, $y);
			$r = ($cor >> 16) & 0xFF;
			$g = ($cor >> 8) & 0xFF;
			$b = $cor & 0xFF;
			switch($efeito){
			case "adicao" : $luz = ($r + $g + $b)/-6 + 127;	$corFinal = imagecolorallocatealpha($img2, $r, $g, $b, $luz); break;
			case "multiply" : $luz = ($r + $g + $b)/6;	$corFinal = imagecolorallocatealpha($img2, $r, $g, $b, $luz); break;
			case "red" : $luz = 0;	$corFinal = imagecolorallocatealpha($img2, $r, 0, 0, $luz); break;
			}
			imagesetpixel($img2, $x, $y, $corFinal);
			}
		}
    	imageAlphaBlending($img2, true);
	    imageSaveAlpha($img2, true);
	    imageAlphaBlending($destino, true);
  		imageSaveAlpha($destino, true);
		imagecopy($destino, $img2, $posx, $posy, 0, 0, imagesx($img2), imagesy($img2));
	}
	
	public function grayscale($configuracoesUser=""){
	$img = $this->img;
	$default = array('constraste'=>25, 'brilho'=>20);
	if(is_array($configuracoesUser)){
	$default = array_merge($default, $configuracoesUser);
	}
	$contraste = $default['constraste'] * -1;
	$brilho = $default['brilho'];
	
	imagefilter($img, IMG_FILTER_GRAYSCALE);
	imagefilter($img, IMG_FILTER_CONTRAST, $contraste);
	imagefilter($img, IMG_FILTER_BRIGHTNESS, $brilho);
	}
	
	public function negativo($img){
	imagefilter($img, IMG_FILTER_NEGATE);
	}
	public function rabisco($img){
	imagefilter($img, IMG_FILTER_EDGEDETECT);
	$this->pb($img);
	}
	
	public function blur($img, $n=3){ #n é o numero de vezes que deve ser repetida a função;
		for($v =0; $v<$n; $v++){
		imagefilter($img, IMG_FILTER_GAUSSIAN_BLUR);
		}
	}
	public function autoAjuste($img){
	$luz = 0;
		for($y = 0; $y<imagesy($img); $y++){
				for($x = 0; $x<imagesx($img); $x++){
				$cor = imagecolorat($img, $x, $y);
				$r = ($cor >> 16) & 0xFF;
				$g = ($cor >> 8) & 0xFF;
				$b = $cor & 0xFF;
				$luz += ($r + $g + $b)/3;
				}
		}
	$luzMedia = $luz / (imagesy($img)*imagesx($img));
	echo $luzMedia."<br/>";
	$ajusteBrilho = (127 - $luzMedia )*0.8;
	$ajusteContraste = ($ajusteBrilho > 0) ? $ajusteBrilho/3 : $ajusteBrilho/-2;
	$limiteBrilho = 40;
	
		if($ajusteBrilho > 0){
			$limiteContraste = 10;
			$ajusteBrilho = ($ajusteBrilho > $limiteBrilho) ? $limiteBrilho : $ajusteBrilho;
		
		} else {
			$limiteContraste = 30;
			$ajusteBrilho = ($ajusteBrilho < $limiteBrilho * -1) ? $limiteBrilho*-1 : $ajusteBrilho;
		}
		
	$ajusteContraste = ($ajusteContraste > $limiteContraste) ? $limiteContraste : $ajusteContraste;
	echo $ajusteContraste; 
	$this->brilho_contraste($img, $ajusteBrilho, $ajusteContraste);
	
	}
	
	public function sepia($img){
	imagefilter($img, IMG_FILTER_GRAYSCALE);
	imagefilter($img, IMG_FILTER_COLORIZE, 90, 55, 30);
	}
	public function cromar($img){
	$img2 = imagecreatetruecolor(imagesx($img),imagesy($img));
	$copia = imagecreatetruecolor(imagesx($img), imagesy($img));
	imagecopy($copia, $img, 0, 0, 0, 0, imagesx($img), imagesy($img));
	imagefilter($copia, IMG_FILTER_EDGEDETECT);
	imagefilter($copia, IMG_FILTER_CONTRAST, -25);
	imagefilter($copia, IMG_FILTER_GRAYSCALE);
	imagecopymerge($img, $copia, 0, 0, 0, 0, imagesx($img), imagesy($img), 20);
	imagefilter($img, IMG_FILTER_SELECTIVE_BLUR);
	imagefilter($img, IMG_FILTER_GAUSSIAN_BLUR);
	imagefilter($img, IMG_FILTER_GAUSSIAN_BLUR);
	imagefilter($img, IMG_FILTER_GRAYSCALE);
	$this->brilho_contraste($img, 0, 35);
	imagecopymerge($img, $copia, 0, 0, 0, 0, imagesx($img), imagesy($img), 30);
	$this->grayscale($img);
	}
//--------------------------------------------**Funções de corte**-------------------------//
	public function crop($x,$y,$w,$h){
		$img = $this->img;
		$xi = imagesx($img);
		$yi = imagesy($img);
		$imgDis = $this->imageCreateTransparent($w,$h);
		imagecopyresampled($imgDis, $img, 0, 0, $x, $y, $xi,$yi, $xi,$yi);
		$this->img = $imgDis;
		return $imgDis;
	}
//--------------------------------------------**Marca D'agua!**------------------------------------------------------//
	public function marca_agua($marcaAgua, $fotoEntrada, $confUser=""){
	$default = array(
	"opacidade" => 100,
	"posicao" => "centro",
	"camada" => "nao"
	);
	if(is_array($confUser)){
	$default = array_merge($default, $confUser);
	}
	$posicao = $default['posicao'];
	$opacidade = $default['opacidade'];
	$camada = $default['camada'];
	
	$fotoW = imagesx($fotoEntrada);
	$fotoH = imagesy($fotoEntrada);
	//informações sobre a marca d'água
	$marca_w = imagesx($marcaAgua);
	$marca_h = imagesy($marcaAgua);
	if(is_array($posicao)){
	$posx = $posicao[0];
	$posy = $posicao[1];
	} else {
		switch($posicao){
		case "centro": $posx = $fotoW - ($fotoW/2)-($marca_w/2); $posy = $fotoH - ($fotoH/2)-($marca_h/2); break;
		case "SE": $posx = 5; $posy = 5; break;
		case "SD": $posx = $fotoW - $marca_w - 5; $posy = 5; break;
		case "IE": $posx = 5; $posy = $fotoH - $marca_h - 5; break;
		case "ID": $posx = $fotoW - $marca_w - 5; $posy = $fotoH - $marca_h - 5; break;
		default: $posx = $fotoW - ($fotoW/2)-($marca_w/2); $posy = $fotoH - ($fotoH/2)-($marca_h/2); break;
		}
	}

	if($camada == "nao"){
	imagecopymerge($fotoEntrada, $marcaAgua, $posx, $posy, 0, 0, $marca_w, $marca_h, $opacidade);
	echo "oie =(";
	} else {
	$this->efeitoCamada($camada, $marcaAgua, $fotoEntrada, $posx, $posy);
	}
	}
//--------------------------------------------**Funções de Redimensionamento!**------------------------------------------------------//
	public function redimensiona_absoluto($x, $y){
	$img2 = imagecreatetruecolor($x, $y);
	$img = $this->img;
	imagecopyresampled($img2, $img, 0, 0, 0, 0, $x, $y, imagesx($img), imagesy($img));
	$this->img = $img2;
	return $img2;
	}
	
	public function imageCreateTransparent($x, $y) {
	$img2 = imagecreatetruecolor($x, $y);
	imageSaveAlpha($img2, true);
	imageAlphaBlending($img2, false);
	$transparent = imageColorAllocateAlpha($img2, 0, 0, 0, 127);
	imagefilledrectangle($img2, 0, 0, imagesx($img2), imagesx($img2), $transparent);
	return $img2;
	}

	public function redimensiona_max_proporcional($xf, $yf){ //(arquivo de imagem, tamanho no eixo x (largura), tamanho no eixo y (altura))
	//Definindo var/áriaveis
	$img = $this->img;
	imagealphablending($img, false); 
	imagesavealpha($img, true); // save alphablending setting (important)
	$xi = imagesx($img);
	$yi = imagesy($img);
	$px = $xf/$xi; //definindo as porcentagens  relacao ao eixo x
	$py = $yf/$yi; //definindo as porcentagens  relacao ao eixo y
	$imgDis = $this->imageCreateTransparent($xf,$yf);
		if($xi >= $xf and $yi >= $yf){ //Se a imagem for maior que o tamanho estipulado em alguma dimensão
			$imgDis = $this->imageCreateTransparent($xf,$yf);
			if($px > $py) {
				$xfp = $xi * $px;
				$yfp = $yi * $px;
				$margemTop = ($yfp - $yf)/2;
				imagecopyresampled($imgDis, $img, 0, 0, 0, 0,$xfp, $yfp, $xi, $yi);
			} elseif ($py > $px) {
				$xfp = $xi * $py;
				$yfp = $yi * $py;
				$margemTop = ($xfp - $xf)/2;
				imagecopyresampled($imgDis, $img, 0, 0, 0, 0,$xfp, $yfp, $xi, $yi);
			} else {
				$xfp = $xi * $px;
				$yfp = $yi * $px;
				imagecopyresampled($imgDis, $img, 0, 0, 0, 0,$xfp, $yfp, $xi, $yi);
			}
			$this->img = $imgDis;
		}	elseif ($xi > $xf and $yi < $yf) {
			$margemLeft = ($xi - $xf)/-2;
			$imgDis = $this->imageCreateTransparent($xf,$yi);
			imagecopyresampled($imgDis, $img, $margemLeft, 0, 0, 0,$xi, $yi, $xi, $yi);
			$this->img = $imgDis;		
		}   elseif ($xi < $xf and $yi > $yf) {
			$margemTop = ($yi - $yf)/-2;
			$imgDis = $this->imageCreateTransparent($xi,$yf);
			imagecopyresampled($imgDis, $img, 0, $margemTop, 0, 0,$xi, $yi, $xi, $yi);
			$this->img = $imgDis;
		}
	}
	public function redimensiona_max_proporcional_sc($xf, $yf){ //(arquivo de imagem, tamanho no eixo x (largura), tamanho no eixo y (altura))
		//Definindo var/áriaveis
		$img = $this->img;
		imagealphablending($img, false); 
		imagesavealpha($img, true); // save alphablending setting (important)
		$xi = imagesx($img);
		$yi = imagesy($img);
		$px = $xf/$xi; //definindo as porcentagens  relacao ao eixo x
		$py = $yf/$yi; //definindo as porcentagens  relacao ao eixo y
		$imgDis = $this->imageCreateTransparent($xf,$yf);
		if($xi >= $xf or $yi >= $yf){ //Se a imagem for maior que o tamanho estipulado em alguma dimensão
			$imgDis = $this->imageCreateTransparent($xf,$yf);
			if($px > $py) {
				$xfp = $xi * $py;
				$yfp = $yi * $py;
			} elseif ($py > $px) {
				$xfp = $xi * $px;
				$yfp = $yi * $px;
			} else {
				$xfp = $xi * $px;
				$yfp = $yi * $px;
			}
			$imgDis = $this->imageCreateTransparent($xfp,$yfp);
			imagecopyresampled($imgDis, $img, 0, 0, 0, 0,$xfp, $yfp, $xi, $yi);
			$this->img = $imgDis;
		}

	}
	//--------------------------------------------**Funções de Arquivo!**------------------------------------------------------//
	public function novaimg($path){
	$srcimage=imagecreatefromstring(file_get_contents($path));
	$width = imagesx($srcimage);
	$height = imagesy($srcimage);
	$dstimage = imagecreatetruecolor($width,$height);
	imagealphablending($dstimage, false); 
	imagesavealpha($dstimage, true); // save alphablending setting (important)
	imagecopyresampled($dstimage,$srcimage,0,0,0,0, $width,$height,$width,$height);
	return $dstimage;
    }
    
    public function getWidth(){
    	return imagesx($this->img);
    }
    public function getHeight(){
    	return imagesy($this->img);
    }

    public function matchProportion($x,$y,$margem=3){
    	$proportion = $x/$y;
    	$h = imagesy($this->img);
    	$w = imagesx($this->img);
    	$proportionImg = $w/$h;
    	$margem = $margem/100;
    	if(($proportionImg - $proportion) <= $margem and ($proportionImg - $proportion) >= $margem*-1){
    		return true;
    	} else {
    		return false;
    	}
    }

	public function extensao($path){
	$extensao =  end(explode(".", $path));	
	return $extensao;
	}
	
	public function converte_foto($foto, $formato){
	$img = imagecreatefromstring(file_get_contents($foto));
	$extensao = end(explode(".", $foto));
	$nome = substr($foto, 0, (strlen($extensao)+1)*-1);
		switch($formato){
		case "jpeg": $foto_final = $nome.".jpeg";	imagejpeg($img, $foto_final,99);	break;
		case "jpg": $foto_final = $nome.".jpg";	imagejpeg($img, $foto_final,99);	break;
		case "web": $foto_final = $nome.".jpg";	imagejpeg($img, $foto_final, 72);	break;
		case "gif":  $foto_final = $nome.".gif";	imagegif($img, $foto_final);	break;
		case "png":  $foto_final = $nome.".png";	imagepng($img, $foto_final);	break;
    	}
	return $foto_final;
	}
	
	public function verifica_imagem($arquivo, $extensoesPermitidas=" jpg jpeg png gif "){
		if(isset($arquivo)){
		$extensao =  end(explode(".", $arquivo));	
			if(strripos($extensoesPermitidas, $extensao)){
			return "foto";
			} else {
			return false;
			}
		} else {
		echo "Erro, a função espera pelo menos um parâmetro.";
		return false;
		}
	}
	public function save($nome=false){
	$nome = (!$nome) ? $this->path : $nome;
	$ext = explode(".", $nome);
	$extensao = end($ext);
	$img = $this->img;
		switch($extensao){
		case "jpg": imagejpeg($img, $nome,99); break;
		case "JPG": imagejpeg($img, $nome,99); break;
		case "jpeg": imagejpeg($img, $nome,99); break;
		case "JPEG": imagejpeg($img, $nome,99); break;
		case "gif": imagegif($img, $nome); break;
		case "GIF": imagegif($img, $nome); break;
		case "png": imagepng($img, $nome); break;
		case "PNG": imagepng($img, $nome); break;
		case "gd": imagegd($img, $nome); break;
		case "GD": imagegd($img, $nome); break;
		}
	}
}

?>