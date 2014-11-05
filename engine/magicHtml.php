<?php

require_once(path_engine_library."/phpbrowsercap.php");
use phpbrowscap\Browscap;

	class MagicHTML {
		//Conteúdo html
		protected $content;
		//Definindo Variáveis Responsáveis pelo armazenamento de CSS's
		public $css_linked;
		public $css_inline;
		//Definindo Variáveis Responsáveis pelo armazenamento de JS's
		public $js_linked;
		public $js_inline;
		//Definindo Variáveis Responsáveis pelo gerenciamento de metainformações
		protected $title;
		protected $metas;
		protected $body;
		protected $site_container;
		protected $site_container_class;
		protected $doctype;
		protected $html_lang;
		protected $headAppends = array();

		public $body_id;
		public $body_class;

		//Definindo outras Variáveis
		protected $requires;
		protected $datamgr;
		protected $output;
		protected $browser;
		protected $mobileDetect;
		//Definindo Variáveis de tratamento de erro
		protected $errors;
		protected $warnings;
		//Path Variables
		protected $cssScopes = array();
		protected $base_cache = base_cache;
		protected $path_cache = path_cache;
		protected $base_css = base_css;
		protected $path_css = path_css;
		protected $path_js = path_js;
		protected $path_system_js = path_system_js;
		protected $path_common_js = path_common_js;

		protected $base_common_js = base_common_js;
		protected $engine_path_css = path_engine_css;
		protected $path_common_css = path_common_css;
		protected $registry = false;

		public function __construct(){
			global $registry;
			$this->registry = $registry;
			$this->css_inline = array();
			$this->css_linked = array();
			$this->js_linked = array();
			$this->js_inline = array();
			$this->title = "";
			$this->metas = array("charset"=>"charset='utf8'");
			$this->body = "";
			$this->body_id = "page";
			$this->body_class = "";
			$this->site_container_class = "";
			$this->site_container = "all-site";
			$this->doctype = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
			$this->html_lang = "pt-br";
			$this->requires = array();
			$this->errors = array();
			$this->warnings = array();
			$this->cssScopes = array(
				"system" => path_engine_css,
				"theme"  => path_css,
				"common" => path_common_css
			);
			
			
			if(!is_dir($this->path_cache) or !is_writable($this->path_cache)){
				die("Check path_cache variable");
			}
			if(!is_dir($this->path_css)){
				die("Check path_css variable(\"".$this->path_css."\")");
			}
			$this->add_system_css("common.css",'all',1);
			$this->add_system_js("jquery-1.9.1.js",true,true);
			$this->add_system_js("ajax.js",true,true);


			$this->add_system_js("elquery.js",true,false);
			$browser = new Browscap($this->path_cache);
			$this->browser = $browser->getBrowser();
			$mobileDetect = new Mobile_Detect();
			$this->mobileDetect = $mobileDetect;
			unset($browser);
		}
		//Funções gerenciamento de links de css
		public function add_css_linked($link, $media="all", $is_local=true,$path=false){
			$base = false;
			if($is_local){
				$path = ($path) ? $path : $this->path_css;
				$base = str_replace(path_root, path_base, $path);
				if(!is_file($path."/".$link)){
					$backTrace = debug_backtrace();
					$callee    = next($backTrace);
					debug::warning("JS $path/$link não existe, e portanto não foi adicionado ao site.",$callee);
					return false;
				}
			}
			$path = $path."/".$link;
			$this->css_linked[] =  array("link"=>$link, "media"=>$media, "is_local"=>$is_local,"path"=>$path,"base"=>$base);
		}
		public function add_css($link, $media="all", $is_local=true,$path=false){
			$this->add_css_linked($link, $media, $is_local,$path);
		}
		public function add_css_scope($scope,$link,$media="all"){
			if(isset($this->cssScopes[$scope])){
				$path = $this->cssScopes[$scope];
				$this->add_css($link,$media,1,$path);
			}
		}
		public function add_system_css($link, $media="all", $is_local=true){
			$this->add_css_linked($link, $media="all", $is_local=true,$this->engine_path_css);
		}
		public function add_common_css($link, $media="all"){
			$this->add_css_linked($link, $media, 1,$this->path_common_css);
		}


		public function drop_css_linked($link){
			if(isset($this->page->css_linked[$link])){
				unset($this->page->css_linked[$link]);
				
			} else {
				$this->warnings[] = "CSS that you tried to drop haven't been added";
				$this->print_errors();

			}
		}
		public function add_css_inline($css=array()){
			if(is_array($css)){
				foreach($css as $obj => $style){
					if(!isset($this->css_inline[$obj])){
						$this->css_inline[$obj] = $style; 	
					} else {
						$this->css_inline[$obj] = $this->css_inline[$obj]." ".$style;
					}
					
				}
			} else {
				$this->errors[] = "function add_css_inline expects parameter \$css to be an array";
				$this->print_errors();
			}
		}
		//Funções gerenciamento de links js

		public function add_js_linked($link, $is_local=true, $js_top = false, $path=false){
			$base = false;
			if($is_local){ 

				$path = ($path) ? $path : $this->path_js;
				if(!is_file($path."/".$link)){
					$backTrace = debug_backtrace();
					$callee    = next($backTrace);
					debug::warning("JS $path/$link não existe, e portanto não foi adicionado ao site.",$callee);
					return false;
				}
				$base = str_replace(path_root, path_base, $path);
			}
			$this->js_linked[$link] =  array("link"=>$link, "is_local"=>$is_local, "js_top"=>$js_top,"path" =>$path,'base'=>$base);
		}
		public function add_js($link, $is_local=true, $js_top = false, $path=false){
			$this->add_js_linked($link, $is_local, $js_top, $path);
		}
		public function add_common_js($link,$js_top=false){
			$this->add_js_linked($link,1,$js_top,$this->path_common_js);
		}
		public function add_system_js($link,$js_top=false){
			$this->add_js_linked($link,1,$js_top,$this->path_system_js);
		}
		public function drop_js_linked($link){
			if(isset($this->js_linked[$link])){
				unset($this->js_linked);
			} else {
				$this->warnings[] = "JS that you tried to drop haven't been added";
				$this->print_errors();

			}
		}
		public function add_js_inline($script){
			$this->js_inline[] = $script;
		}

		//funções para gerenciamento de metainformações
		public function appendToHead($alias,$content){

			$this->headAppends[$alias] = $content;
			return true;
		}
		public function unappendFromHead($alias){
			
			if(isset($this->headAppends[$alias])){
				unset($this->headAppends[$alias]);
				return true;
			} else {
				return false;
			}
		}
		public function set_body_class($class){
			$this->body_class = $class;
		}

		public function set_site_container_class($class){
			$this->site_container_class = $class;
		}
		public function set_title($title){
			$this->title = $title;
		}
		public function add_meta($name, $content){
			$this->metas[$name] = $content;
		}
		public function drop_meta($name){
			if(isset($this->metas[$name])){
				unset($this->metas[$name]);
				return true;
			} else {
				return false;
			}
		}
		public function set_html_lang($lang){
			$this->html_lang = $lang;
		}
		//Funções retorno de css e js
		public function get_css_links(){
			global $breakpoints, $gridColumns,$gridMargin;
			if(count($this->css_linked) > 0){
			/*global $path_css;
			$css_links = array();
			if(!is_array($this->css_common)){
				$this->css_common = array();
			}
			$css_all = array_merge($this->css_common, $this->css_linked);
			
			foreach($css_all as $link => $css){
				$path = ($css["is_local"] = true) ? $path_css."/".$link : $link;

					$css_link = "<link rel='stylesheet' type='text/css' href='".$path."' media='".$css['media']."' />";				
					$css_links[$link] = $css_link;
				
				
			}
			return $css_links; */
			
			$timeMod = 0;
			$css_links = array();
			$css_all = $this->css_linked;
			$name = scope;
			$content = "";
			foreach($css_all as $k=>$css){
				$link = $css['link'];
				if($css['is_local']){
					$file = $css['path'];
					$mod = filectime($file);
					$timeMod = ($timeMod < $mod) ? $mod : $timeMod;
					$name .= "-".str_replace(".css","",$link);
				}
				else {
					$file = $link;
					$name .= "-".str_replace("/","-",$link);
				}
				$name.="$k";
			}
			$name .= "-".str_replace("/","-",$_SERVER['HTTP_USER_AGENT']);
			$name = md5($name).".css";
			
			$arquivo = $this->path_cache."/$name";
			$modFile = 0;
			if(file_exists($arquivo)){
				$modFile = filectime($arquivo);
			}
			if(($modFile == 0 or $modFile < $timeMod) and count($css_all) > 0){

				require_once(path_engine_library."/lessc.php");
				$less = new lessc;
				$cssFinal = fopen($arquivo,"w+");
				$browser = $this->browser->Browser;
				$isTablet = $this->mobileDetect->isTablet();
				$isMobile = $this->mobileDetect->isMobile();
				$version = $this->browser->Version;
				$MajorVer = $this->browser->MajorVer;
				$MinorVer = $this->browser->MinorVer;
				$equery = array();
				foreach($css_all as $css){
					$link = $css['link'];
					if($css['is_local']){
						$file = $css['path'];
					}
					else {
						$file = $link;
					}
					$base = $css['base'];
					
					$css = file($file);

					foreach($css as $l=>$rule){
						

						if(preg_match("/([^\s]+)@eq\(([^)]+)\)/",$rule,$match)){
							$expression = str_replace(" ","_",strtolower($match[2]));
							$element = $match[1];
							$css[$l] = str_replace($match[0],"$element.$expression",$css[$l]);
							$equery[] = array($element,$match[2]);
						}
						if(preg_match("/^[\s]*if(.+):$/",$rule,$match)){
							$css[$l] = str_replace($match[0], "<?php if(".$match[1].") { ?>",$rule);	
						}
						if(preg_match("/^[ \t\s]*end[ \t\s]*$/",$rule,$match)){
							$css[$l] = str_replace("end", "<?php } ?>",$css[$l]);
						}
						if(preg_match("/else:/",$rule,$match)){
							$css[$l] = str_replace("else:", "<?php } else { ?>",$css[$l]);
						}
						if(preg_match("/elseif  *(.+) *:/",$rule,$match)){
							$css[$l] = str_replace($match[0], "<?php } elseif(".$match[1].") { ?>",$rule);
						}

					}
					$basename = basename($file);

					file_put_contents($this->path_cache."/".$basename, $css);

					$file = str_replace($this->path_css,$this->path_cache,$file);
					ob_start();
					include $file;
			        $min = ob_get_clean();
			        $min = str_replace("css_path", $base, $min);

					
					
/*					$min = file_get_contents($file);
*/					$content .= $min."\n\n";
				}
				unset($browser);
				unset($version);
				unset($MajorVer);
				unset($MinorVer);

				$content = $less->compile($content);
				$content = str_replace("\n","",$content);
				fwrite($cssFinal, $content);
				fclose($cssFinal);
				if(count($equery) > 0){
					$arquivo = $this->path_cache."/equery.js";
					$equeryFile = fopen($arquivo,"w+");
					fwrite($equeryFile, json_encode($equery));
					fclose($equeryFile);
				}
			}
			return array("all"=>"<link rel='stylesheet' type='text/css' href='".$this->base_cache."/$name' media='all' />");
			} else {
				return array("all"=>"");

			}
		}
		public function get_css_inline(){
			if(count($this->css_inline) > 0){
			$css_inline = "<style>";
			foreach($this->css_inline as $obj => $style){
				$css_inline .= "
$obj { $style }";
			}
			$css_inline .= "
</style>";
			return $css_inline;
			} else {
				return false;
			}
		}

		public function get_js_links($pos=false){
			
			$js_links = array();
			$js_all = $this->js_linked;
			if($pos){
				foreach($js_all as $link=>$js){
					$flagTop = ($pos == "top") ? true : false;
					if($js['js_top'] != $flagTop){
						unset($js_all[$link]);
					}
				}
			}
			foreach($js_all as $key => $js){
				$link = $js['link'];
				$path = ($js["is_local"]) ? $js['base']."/".$link : $link;
				$js_link = "<script src=\"$path\"></script>";				
				$js_links[$link] = $js_link;
			}
			return $js_links;
		}
		public function get_js_inline(){
			$arquivo = $this->path_cache."/equery.js";
			$equery = file_get_contents($arquivo);
			$equery = json_decode($equery);
			if(!is_array($equery))
				$equery = array();
			foreach($equery as $query){
				$this->add_js_inline(
					"$(\"{$query[0]}\").equery(\"$query[1]\");");
			}
			$html = '
<script name="js-inline">
			$(document).ready(function(){
			';
			foreach($this->js_inline as $name=>$script){
				$html .= <<<JSINLINE
					$script
				
JSINLINE;
			}
			$html .="
		});
</script>";
			return $html;
		}
		//Funções gerenciamento de erros;
		public function print_errors(){
			foreach($this->warnings as $warning){
				echo "<strong>Warning:</strong> $warning <br />";
			}
			if(count($this->errors) > 0){
				foreach($this->errors as $error){
				echo "<strong>Fatal Error:</strong> $error <br />";
				}
				
				die();
			}
		}
		public function add_warning($warning="", $line=__LINE__, $file=__FILE__){
			$this->warnings[] = $warning." on file $file at line $line";
		}
		//Funções para retorno de código
		public function get_head(){
			$head_html = "<head>";
			foreach($this->metas as $meta){
				$head_html .= "
<meta $meta />";
			}
			$css_links = implode("\n", $this->get_css_links());
			$js_links = implode("\n", $this->get_js_links("top"));
			$css_inline = $this->get_css_inline();
			$headAppends = implode("\n",$this->headAppends);
			$head_html .= "
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head>
<meta name=\"viewport\" content=\"width=device-width,initial-scale=1\">
<title>".$this->title."</title>
<script>
var path_base = '".path_base."';
var magic_scope = '".scope."';
var magic_language = '".magic_language."';

</script>
$css_links
$js_links
$css_inline
$headAppends
</head>";
		return $head_html;
		}
		
		protected function get_content() {
			
			return $this->content;
		}
		public function set_content($content) {
			$this->content = $content;
		}
		public function get_body(){
			$id = $this->body_id;
			$class = $this->body_class;
			$body = $this->get_content();
			$js_links = implode("\n", $this->get_js_links("bottom"));
			$js_inline = $this->get_js_inline();
			$site_container = $this->site_container;
			$site_container_class = $this->site_container_class;
			$html_body = <<<EOD
<body id="$id" class='$class'>
	<div id="$this->site_container" class='$site_container_class'>
	$body

	</div>
	$js_links
	$js_inline
</body>
EOD;
		return $html_body;
	    }
	    
	    public function create(){
	    	$this->print_errors();
	    	$head = $this->get_head();
	    	$body = $this->get_body();
	    	$html = $this->doctype."\n<html lang=\"{$this->html_lang}\">\n".$head."\n".$body."\n</html>";
	    	return $html;

	    }
	    public function render($content){
	    	$this->set_content($content);
	    	echo $this->create();
	    }
	    //Funções extras
	    public function get_requires(){
   			global $path_root, $path_base, $path_models, $path_js, $path_css, $path_common, $path_controllers,$path_datamgr;

			foreach($this->requires as $require){
				require_once($require);
			}
		}

		public function get_data(){
	
	    }
	}	
?>