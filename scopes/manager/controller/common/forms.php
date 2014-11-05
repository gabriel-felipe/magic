<?php 
/**
* 
*/
require_once(path_models."/SqlForm.php");
class ControllerCommonForms extends ManagerController
{
	function index(){
		$SqlForm = new SqlForm;
		$this->basicLayoutTasks();
        $this->set_template('common/forms');
        $dbmanager = new bffdbmanager;
        $tables = $dbmanager->getTables();
   		$this->data['tables'] = $tables;
   		$columns = false;
   		$this->data['fieldStructure'] = false;
   		$this->data['table'] = false;
   		if (isset($_POST['table'])) {
   			$this->data['fieldStructure'] = $SqlForm->parseTable($_POST['table']);
   			$this->data['table'] = $_POST['table'];
   		}
   		$this->data['columns'] = $columns;
   		$this->data['codeGenerator'] = $this->url->get("common/forms/generate");

		echo $this->html->render($this->get_content());
	}
	function generate(){
		$SqlForm = new SqlForm;
		echo "<h2> HTML </h2>";
		echo "<xmp>";
		foreach($_POST['input'] as $input){
			echo $SqlForm->getInputHtml($input);
		}
		echo "</xmp>";
		echo "<h2> Controller </h2>";
		echo "<xmp>";
		echo $SqlForm->getFormInterface($_POST['input'])."\n\n\n";
		echo "</xmp>";
		
	}
}
?>