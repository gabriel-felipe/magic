 <?php
	use Magic\Engine\Datamgr\DbConnect;
    use Magic\Engine\Datamgr\DbManager;
    use Magic\Engine\Datamgr\DbModel;
    use Magic\Engine\Datamgr\DbModelPlural;
    use Magic\Engine\Datamgr\Driver\mysql\DbSelect;
    use Magic\Engine\Datamgr\Driver\DbDriverFactory;
    use Magic\Engine\Document\Link\FavIconLink;
    use Magic\Engine\Adapter\TableToForm;
    use Magic\Engine\Form\Form;
    use Magic\Engine\Form\Decorators as Dec;
    use Magic\Engine\Adapter\ColumnToElement as C2E;
    use Magic\Common\Adapter\ColumnToElement as CommonC2E;
    class ControllerAbout extends publicController {
    	
        public function index(){
            $cnx = new DbConnect();
            $dbManager = DbDriverFactory::getDbManager($cnx);
            $form = new Form("teste");
            $form->addDecorator(new Dec\LabelDecorator());
            $form->addDecorator(new Dec\TagWrapperDecorator("div",array("class"=>"campo","id"=>"teste-[id]-[class]")));

            $tableToForm = new TableToForm($dbManager,$form);
            $tableToForm->addAdapter(new CommonC2E\FkToSelect());
            $tableToForm->addAdapter(new CommonC2E\TinyintToCheckbox());
            $tableToForm->addAdapter(new CommonC2E\TextareaAdapter());
            $tableToForm->addAdapter(new C2E\HiddenPk());
            $tableToForm->addAdapter(new C2E\SimpleInput());
            $form = $tableToForm->getForm("hotel");
            echo $form->render();
            die();
        	$css = $this->scope->getThemeCss("about.css");
        	$css->cor = "#ff0";
        	$this->html->addLink($css);
        	$this->render($this->getContent());

        }

    }
   
?>
