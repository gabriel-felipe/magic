<?php

class Controllerajaxcrud extends controller {
	public function novo(){
		if($this->login->is_logged("1")){
			$attributes = $_POST['fields'];
			$conf = $_POST['conf'];
			$obj = new dbModel($conf['class']);
			$obj->setAtributos($attributes);
			$status = array();
			if($obj->save()){

				if(isset($conf['url_retorno'])){
					$this->url->redirect($conf['url_retorno']);
				}
				if(isset($conf['success'])){
					$this->children = array("common/header", "common/footer");
					$this->template = 'common/success';
					$this->data['success'] = $conf['success'];
					echo $this->create();
				}
				if(isset($_POST['ajax'])){
					$status['status'] = 'success';
					$status['id']     = $obj->id;
					echo json_encode($status);
				}
			}
		}		
	}
	public function del(){
		if($this->login->is_logged("1")){
			$class = data::post('class','no_special');
			$model = data::post('model','no_special');
			if($model){
				require_once(path_models."/".$model.".php");
				$obj = new $class;
			} else {
				$obj = new dbModel($class);
			}
			$obj->find(data::post('id','int'));;
			if($obj->destroy()){
				if(isset($_POST['url_retorno'])){
					$this->url->redirect(data::post('url_retorno','url'));
				}
				if(isset($_POST['success'])){
					$this->children = array("common/header", "common/footer");
					$this->template = 'common/success';
					$this->data['success'] = data::post('success','special_chars');
					echo $this->create();
				}
				if(isset($_POST['ajax'])){
					$response = array("status"=>'success');
					echo json_encode($response);
				}
			}
		}
	}
}
?>