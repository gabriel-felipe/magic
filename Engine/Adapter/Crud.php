<?php 
namespace Magic\Engine\Adapter;
use Magic\Engine\Datamgr\DbModel;
use Magic\Engine\Form\Form; 
/**
* Classe responsável pela abstração de funções básicas de inserção, update e drop de registros no banco.
*/
class Crud 
{
	protected $form;
	protected $dbmodel;
	public $errorCode;
	public $error;
	public $errorData;
	protected $registry;
	function __construct(Form $form, DbModel $dbmodel)
	{
		global $registry;
		$this->registry = $registry; 
		$this->form = $form;
		$this->dbmodel = $dbmodel;

	}

	function save($data,$id=false){
		

		$form = clone $this->form;
		$form->populate($data);

		if ($form->isValid()) {
			$obj = clone $this->dbmodel;
			$obj->find($id);
			$values = $form->getValues();
			$obj->setData($values);
			if ($obj->save()) {
				$obj->find($obj->id);
				return $obj->info();
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
		$majax = $this->registry->get("MAjax")->getInstance();
		if ($obj){
			$majax->setMsg("Salvo com sucesso");
		} elseif($this->errorCode == "SAVE") {
			$majax->setStatusCode(400);
			$majax->setMsg("Erro desconhecido ao salvar");
		} elseif($this->errorCode == "VALIDATE"){
			$majax->setStatusCode(400);
			$majax->setMsg("Erro com a validação");
			$majax->setData($this->errorData);
		}
		$majax->render();
	}

	function delete($id){
		$obj = clone $this->dbmodel;
		$obj->find($id);
		return $obj->destroy();
	}

	function getForm($id=0,$returnHtml=0){
		$obj = clone $this->dbmodel;
		$obj->find($id);
		$form = clone $this->form;
		$form->setDbModel($obj);
		$view = $this->scope->getView("partials/_form");
		$view->form = $form;
		$view->titulo = ($obj->getId()) ? "Editando registro #".$obj->getId()  : "Novo registro";
		$view->delete = ($obj->getId()) ? 1 : 0;
		$view->deleteId = ($obj->getId()) ? $obj->getId() : 0;
		if ($returnHtml) {
			return $view->render();
		}
		$majax = $this->MAjax->getInstance();
		$majax->html = $view->render();
		$majax->render();
	}

	public function __get($key) {
		return $this->registry->get($key);
	}
}
?>

?>