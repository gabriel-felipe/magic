<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/paths_diviege.php");
	if(isset($_GET['action'])){
		if(json_decode($_GET['action'])){
			$action = json_decode($_GET['action'],true);
			if(is_callable($action['action'])){
				$params = (is_array($action['params'])) ? $action['params'] : array();
				$res = array();
				ksort($params);
				try {
					$res = call_user_func_array($action['action'],$params);
					$status = "1";
					$res = array("0"=>$res);
					
				} catch(Exception $e){
					$status = "0";
					$res = array("errormsg"=>$e->getMessage());
					if(method_exists($e, "getParams")){
						$res['errorparams'] = $e->getParams();
					}
				}
				$res['ajaxstatus'] = $status;
				echo json_encode($res);
			}

		}
	}
	function cod_recuperar_senha($email){
		global $path_root,$path_base,$path_models,$path_common,$path_datamgr,$path_controllers;
		require_once($path_models."/usuarios.php");
		require_once($path_common."/class.phpmailer.php");
		$usuarios = new usuarios;
		if($usuarios->where("email = :email",array(":email"=>$email))){

			require_once($path_models."/restaurar_senha.php");
			$senha_nova = new restaurar_senha;
			if(!$senha_nova->where("usuarios_id = :id and valido = :a", array(":id"=>$usuarios->id,":a"=>"1"))){
				$senha_nova->criacao = time();
				$senha_nova->valido = 1;
				$senha_nova->usuarios_id = $usuarios->id;
				$senha_nova->cod = base64_encode(microtime()."_".rand()."_".$usuarios->id);
				$senha_nova->save();
				$mail = new Emailer;
				$mail->Body = $senha_nova->cod;
				$mail->Subject = "Código Troca Senha";
				$mail->AddAddress($email);
				if($mail->send()){
					return "Código Gerado";
				} else {
					throw new Exception("Não foi possível enviar o email. Erro: ".$mail->ErrorInfo." Tente novamente mais tarde.");	
				}
			} else {
				$mail = new Emailer;
				$mail->Body = $senha_nova->cod;
				$mail->Subject = "Código reenviado Troca Senha";
				$mail->AddAddress($email);
				$mail->send();
				return "Código Gerado";
			}
		} else {
			throw new Exception("Não foi encontrado um usuário correspondente ao email");
		}
	}
	function redefine_senha($cod,$email,$senha){
		if($cod and $senha and $email){
			global $path_root,$path_base,$path_models,$path_common,$path_datamgr,$path_controllers;
			require_once($path_models."/usuarios.php");
			require_once($path_common."/class.phpmailer.php");
			require_once($path_models."/restaurar_senha.php");
			$restaurar = new restaurar_senha;
			$usuarios = new usuarios;
			try {
				$valida = valida_codigo($email,$cod);
				if($usuarios->find($valida[0])){
					$usuarios->password = md5($senha);
					if($usuarios->save()){
						$restaurar->find($valida[3]);
						$restaurar->valido = 0;
						$restaurar->save();
						$mail = new Emailer;
						$mail->Body = "Senha mudada com sucesso";
						$mail->Subject = "Troca de senha Diviege";
						$mail->AddAddress($email);
						$mail->send();
						return "Senha alterada com sucesso";
					} else {
						throw new Exception("Ocorreu um erro na busca do usuário no banco de dados.");	
					}
				}
				
			} catch (Exception $e){
				throw $e;
			}
		}
	}
	function valida_codigo($email,$cod){
		if($cod and $email){
			global $path_root,$path_base,$path_models,$path_common,$path_datamgr,$path_controllers;
			require_once($path_models."/usuarios.php");
			require_once($path_common."/class.phpmailer.php");
			require_once($path_models."/restaurar_senha.php");
			$restaurar = new restaurar_senha;
			$usuarios = new usuarios;
			if($usuarios->where("email = :e",array(":e"=>$email))){
				if($restaurar->where("cod = :cod and usuarios_id = :id",array(":cod"=>$cod,":id"=>$usuarios->id))){
					if($restaurar->valido == 1){
						return array($usuarios->id,$cod,$email,$restaurar->id);	
					} else{
						throw new Exception("O código fornecido já não é mais válido");			
					}
				}else {
					throw new Exception("Não foi encontrado um código $cod associado ao usuário");		
				}
			} else {
				throw new Exception("Não foi encontrado um usuário associado ao email $email");		
			}
		}
	}
/*	function get_session_product($column,$order){
		session_start();
		global $path_root,$path_base,$path_models,$path_common,$path_datamgr,$path_controllers;
		require_once($path_models."/produtos.php");
		$produtos = new produtosplural;
		$produtos->get_by_ids($_SESSION['produtos']);
		$produtos->order($column,$order);
		$_SESSION['produtos'] = $produtos->get_element_list();
		return $produtos->get_list();
	}*/
	
	function set_sessionvar($k,$v){
		$public_variables = array("carrinho");
		if(in_array($k, $public_variables)){
			if(session_id() == ""){
				session_start();
			}
			$_SESSION[$k] = $v;
			return array($k=>$v);
		} else {
			throw new Exception("You don't have permission to access this data. ($k)", 1);
			
		}
	}
	function get_sessionvar($k){
		$public_variables = array("carrinho");
		if(in_array($k, $public_variables)){
			if(session_id() == ""){
				session_start();
			}
			return (isset($_SESSION[$k])) ? array("0"=>$_SESSION[$k]) : false;
		} else {
			throw new Exception("You don't have permission to access this data. ($k)", 1);
			
		}
	}
	function prepare_product($id){
		global $path_base,$path_root,$path_common,$path_models,$path_controllers,$path_datamgr;
		require_once($path_models."/produtos.php");
		$produtos = new produtos;
		$produtos->find($id);
		$c = 0;
		$coresHtml = "";
		$cores = $produtos->get_cores();
		foreach($cores as $idc=>$cor){
			$pri = ($c == 0) ? "pri" : "";
			$style = ($cor['textura'] != "") ? "url(".$path_base.$cor['textura'].")" : $cor['hex_code'];
			$off = ($cor['qtn'] == 0) ? "reallyoff" : "";
			$coresHtml .= "<div class=\"cor $pri $off\"  qtn=\"{$cor['qtn']}\" corId='$idc' produtoId='$id' style=\"background: $style;\"></div>
			";
			$c++;
		}
		$tamanhos = $produtos->get_tamanhos();
		$tamanhosHtml = "";
		$c = 0;
		foreach($tamanhos as $idt=>$tamanho){
			$class = ($tamanho['qtn'] == 0) ? "reallyoff" : "";
			$tamanhosHtml .= "<div class=\"tamanho $class\" tamanhoId=\"$idt\" qtn=\"{$tamanho['qtn']}\" produtoId='$id'>{$tamanho['tamanho']}</div>
			";
			$c++;
		}
		$pecas = new pecas("produtos_id = :p",array(":p"=>$id));
		$pecasInfo = $pecas->info();
		$cores_tamanhos = array();
		$tamanhos_cores = array();
		$fotos = new fotosplural;
		$cores_fotos = array();
		$cores_fotos[0] = $fotos->where("produtos_id = :id and cores_id = :c",array(":id"=>$id,":c"=>"0"));
		$todas_fotos = $fotos->where("produtos_id = :id", array(":id"=>$id));
		

		foreach($pecas->plural as $p=>$peca){
			$t = $peca->tamanhos_id;
			$c = $peca->cores_id;
			$qtn = $peca->quantidade;
			if(!$cores_tamanhos[$c]){
			$cores_tamanhos[$c] = array();
			}
			if(!$tamanhos_cores[$t]){
			$tamanhos_cores[$t] = array();
			}

			$qtn = $peca->quantidade;
			
			$cores_tamanhos[$c]['lista'][] = $t;	
			$cores_tamanhos[$c][$t] = $qtn;
			$tamanhos_cores[$t]['lista'][] = $c;	
			$tamanhos_cores[$t][$c] = $qtn;			
			if($cores_fotos[$c] and $c){				
				$cores_fotos[$c] = $fotos->where("produtos_id = :id and cores_id = :c",array(":id"=>$id,":c"=>$c));
			}
		}

		$resultado = array(
			"pecas"=>$pecasInfo,
			"todas_fotos" => $todas_fotos,
			"cores_fotos" => $cores_fotos,
			"cores_tamanhos" => $cores_tamanhos,
			"tamanhos_cores" => $tamanhos_cores,
			"cores_html" => $coresHtml,
			"tamanhos_html" => $tamanhosHtml
		);

		return $resultado;
	}

	
	if(isset($_GET['model'])){
		if(json_decode($_GET['model'])){
			$model = json_decode($_GET['model'],true);
			if(isset($model['file']) and isset($model['action']) and isset($model['class'])){
				$params = (is_array($model['params'])) ? $model['params'] : array();
				$defaultAction = ($model['defaultAction'] == "false") ? false : true;
				$id = (isset($model['id'])) ? $model['id'] : false;
				call_default_action($model['file'],$model['class'],$model['action'],$params,$id,$defaultAction);	
			}	
		} else {
			echo "Erro no decode do JSON ".$_GET['model'];
			
		}
	}
	function call_default_action($file,$class,$action,$params=array(),$id=false,$defaultAction=true){
		global $path_models,$path_root,$path_base,$path_common,$path_controllers,$path_layout,$path_datamgr,$path_views,$path_js,$path_css,$path_img,$path_images;
		$public_operations = array(
			"fotos/fotosplural"=>array("where"),
			"carrinho/carrinho"=>array("valor_carrinho","set_qtn","get_carrinho_info","add_peca","removepeca","get_frete","get_desconto"),
		);
		if(array_key_exists("$file/$class", $public_operations) and in_array($action, $public_operations["$file/$class"])){
			if(is_file($path_models."/".$file.".php")){
				require_once($path_models."/".$file.".php");
				if(class_exists($class)){							
					if($defaultAction){
						switch($action){
							case"get_info": 
								if($id) {
									$obj = new $class;
									$obj->find($id);
									echo json_encode($obj->info());
								}	else {
									die("erro, id não definido!");
								}
								break;		
						}
					} else {
						$obj = new $class;
						if(method_exists($obj, "find")){
							$obj->find($id);
						}
						if(method_exists($obj, $action)){
							ksort($params);
							try {
								$res = call_user_func_array(array($obj, $action),$params);
								$status = "1";
								if(!is_array($res)){
									$res = array("0"=>$res);
								}
							} catch(Exception $e){
								$status = "0";
								$res = array("errormsg"=>$e->getMessage());
								if(method_exists($e, "getParams")){
									$res['errorparams'] = $e->getParams();
								}
							}
							$res['ajaxstatus'] = $status;
							echo json_encode($res);
						} else {
							die("Método $action não existe.");
						}
					}
				} else {
					die("Classe $class não existe");
				}
		
			} else {
				echo "{error:\"arquivo não encontrado\"}";
			}
		} else {
			echo "{error:\"permissão não concedida\"}";
		}
	}
?>