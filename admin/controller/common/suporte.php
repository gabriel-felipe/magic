<?php
	class Controllercommonsuporte extends Controller{
		public function index(){
			
			if($this->login->is_logged("1")){

			$this->children = array("common/header", "common/footer");
			$this->add_css_linked('suporte.css');
			$this->template = 'suporte/index';
			
			$tickets = do_post_request("http://www.webingpro.com.br/intranet-beta/index.php?ns=webservice&route=suporte/ticket/get_by_token",array('token'=>$this->logged['token_suporte']));

			
    		$tickets = json_decode($tickets,true);
    		$tickets = (isset($tickets['tickets'])) ? $tickets['tickets'] : array();
    		if($tickets){
    			foreach($tickets as $t=>$ticket){
    				$tickets[$t]['hora'] = (date('Y') == date("Y", $ticket['hora'])) ? date("d/m \a\s h:i", $ticket['hora']) : date("d/m/y \a\s h:i", $ticket['hora']) ;
    				switch($ticket['status']){
    					case 0: 
    						$tickets[$t]['status'] = 'Aguardando resposta do suporte';
    						$tickets[$t]['status-class'] ='brown';
    						break;
    					case 1:
    						$tickets[$t]['status'] = 'Aguardando sua resposta';
    						$tickets[$t]['status-class'] = 'orange';
    						break;
    					case 2:
    						$tickets[$t]['status'] = 'Resolvido';
    						$tickets[$t]['status-class'] ='green';
    						break;
    				}
    				$tickets[$t]['link'] = $this->url->get('common/suporte/ticket',array('id'=>$ticket['id']));
    			}
    		}

    		$this->data['tickets']    = $tickets;
			$this->data['novo_link']  = $this->url->get('common/suporte/novo');

    		unset($tickets);
			echo $this->create();
			} else {
				$this->url->redirect('common/login', array("erro"=>$this->login->msgErro));
			}
		}
		public function ticket(){	
			if($this->login->is_logged("1")){
			$this->add_js_linked("ckeditor/ckeditor.js",true,true);
			$this->children = array("common/header", "common/footer");
			$this->add_css_linked('suporte.css');
			$this->template = 'suporte/ticket';
			$info = do_post_request("http://www.webingpro.com.br/intranet-beta/index.php?ns=webservice&route=suporte/ticket/info",array("token"=>$this->logged['token_suporte'],"ticket"=>data::get('id','int')));
			$info = json_decode($info,true);
				if($info['status'] == 'success'){
					$info = $info['info'];
					
					$this->data = array_merge($this->data,$info);
					$this->data['hora'] = (date('Y') == date("Y", $this->data['hora'])) ? date("d/m \a\s h:i", $this->data['hora']) : date("d/m/y \a\s h:i", $this->data['hora']) ;
					switch($this->data['status']){
						case 0: 
							$this->data['status'] = 'Aguardando resposta do suporte';
							$this->data['status_class'] ='brown';
							break;
						case 1:
							$this->data['status'] = 'Aguardando sua resposta';
							$this->data['status_class'] = 'orange';
							break;
						case 2:
							$this->data['status'] = 'Resolvido';
							$this->data['status_class'] ='green';
							break;
					}
					$this->data['resolvido'] = $this->data['status_class'] == 'green';



					$respostas = do_post_request("http://www.webingpro.com.br/intranet-beta/index.php?ns=webservice&route=suporte/ticket/get_respostas",array("token"=>$this->logged['token_suporte'],"ticket"=>data::get('id','int')));
					
					$respostas = json_decode($respostas,true);
					$respostas = $respostas['respostas'];
					foreach($respostas as $k=>$r){
						$respostas[$k]['hora'] = (date('Y') == date("Y", $r['hora'])) ? date("d/m \a\s h:i", $r['hora']) : date("d/m/y \a\s h:i", $r['hora']) ;
					}
					
					$this->data['respostas'] = $respostas;
					$this->data['action'] = $this->url->get('ajax/suporte/responder');
				} else {
					$this->template = 'common/fail';
					$this->data['erro'] = $info['error'];

				}
			echo $this->create();
				
			} else {
					$this->url->redirect('common/login', array("erro"=>$this->login->msgErro));
			}
		}

		public function novo(){
			if($this->login->is_logged("1")){
			$this->add_js_linked("ckeditor/ckeditor.js",true,true);
			$this->children = array("common/header", "common/footer");
			$this->add_css_linked('suporte.css');
			$this->template = 'suporte/novo';
			$this->data['action'] = $this->url->get('common/suporte/tsave');
			echo $this->create();
			} else {
				$this->url->redirect('common/login', array("erro"=>$this->login->msgErro));
			}
		}
		public function tsave(){
			$novo = do_post_request("http://www.webingpro.com.br/intranet-beta/index.php?ns=webservice&route=suporte/ticket/novo",$_POST);
			$novo = json_decode($novo,true);
			if($novo['status'] == 'success'){
				$this->url->redirect('common/suporte');
			} else {
				echo "Erro =/: <br />".print_r($novo);
			}
		}
		
	}
?>