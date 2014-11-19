<?php 
/**
* Classe responsável pela abstração de funções básicas de inserção, update e drop de registros no banco.
*/
class crud 
{
	protected $table;
	protected $form;
	protected $dbmodel;
	public $errorCode;
	public $error;
	public $errorData;
	protected $controller;
	function __construct($table,IForm $form, DbModel $dbmodel,Controller $controller)
	{
		$this->table;
		$this->form = $form;
		$this->dbmodel = $dbmodel;
		$this->controller = $controller;
	}

	function save($data,$id=false,$template=false){
		

		$form = clone $this->form;
		$form->populate($data);

		if ($form->isValid()) {
			$obj = clone $this->dbmodel;
			$obj->find($id);
			$values = $form->getValues();
			$obj->setAtributos($values);
			if ($obj->save()) {
				$obj->find($obj->id);
				$retorno = $obj->info();
				if ($template) {
					$retorno['html'] = $this->controller->get_view($template,$retorno);
				}
				return $retorno;
			} else {
				$this->error = "Erro ao Salvar";
				$this->errorCode = "SAVE";
				return false;
			}
		} else {
			$this->error = "";
			$this->errorCode = "VALIDATE";
			$error = "";
			foreach($form->getValidateErrors() as $campo => $erros){
				$error .= "$campo\n";
				foreach($erros as $e){
					$error .= "\t$e\n";
				}
			}
			$error = "Ocorreram erros na validação: \n".$error;
			$this->error = $error;
			$this->errorData = $form->getValidateErrors();
			return false;
		}
	}

	function jsonSave($data,$id,$template=false){
		$obj = $this->save($data,$id,$template);
		if ($obj){
			return $this->controller->json->success("Salvo com sucesso",$obj);
		} elseif($this->errorCode == "SAVE") {
			return $this->controller->json->fail("Erro desconhecido ao salvar");
		} elseif($this->errorCode == "VALIDATE"){
			return $this->controller->json->fail($this->error,$this->errorData);	
		}
	}

	function delete($id){
		$obj = clone $this->dbmodel;
		$obj->find($id);
		return $obj->destroy();
	}
}
?>