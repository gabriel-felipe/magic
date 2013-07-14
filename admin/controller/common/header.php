<?php
	class Controllercommonheader extends Controller{
		public function index(){
			if($this->login->is_logged("1")){
				// $this->add_css_linked("http://fonts.googleapis.com/css?family=PT+Serif",'all',false);
				$this->add_css_linked("style_guide.css");
				$this->add_css_linked("style-guide-plus.css");

				$this->add_css_linked("font-awesome.css");
				$this->add_js_linked('ajax.js');
				$this->add_js_linked('jquery-2.0.js', true,true);
				$this->template = 'common/header';
				/*DEFINING LINKS*/
				$this->data['avatar']      = $this->logged['avatar'];
				$this->data['home']        = $this->url->get('common/home');
				$this->data['suporte']     = $this->url->get('common/suporte');
				$this->data['page1']       = 'Representantes';
				$this->data['linkpage1']   =  $this->url->get('representantes/index');
				$this->data['classpage1']  = 'icon-group';

				$this->data['opcoes']      = $this->url->get('catalogo/opcoes');
				$this->data['tipospecas']  = $this->url->get('catalogo/tipo-pecas');
				$this->data['pecas']       = $this->url->get('catalogo/pecas');
				$this->data['kitQuadro']   = $this->url->get('catalogo/kits/quadros');
				$this->data['kitBicicleta']= $this->url->get('catalogo/kits/bicicletas');
				$this->data['site']        = $this->url->get('common/home',array("ns"=>'public'),'public');
				$this->data['logout'] = $this->url->get("common/logout");
				/*DEFINING TEXT INFO*/			
				$this->data['logged'] = $this->logged['nome']." ".$this->logged['sobrenome'];
				/*GETTING CONTENT*/
				echo $this->get_content();
			} else {
				$this->url->redirect('common/login', array("erro"=>$this->login->msgErro));
			}
			

		}
	}
?>