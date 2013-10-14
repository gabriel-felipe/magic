<?php
    class teste extends dbModel
    {
        public function __construct($fields=false,$query=false,$queryParams=false)
        {
            parent::__construct("noovo", $fields=false,$query=false,$queryParams=false); 
        }
    }
    class testes extends dbModelPlural
    {
        public teste = "teste";
        public function __construct($fields=false,$query=false,$queryParams=false,$plural=array(),$page=1,$qtnbypage=9999999999)
        {                   
            parent::__construct("noovo", $fields,$query,$queryParams,$plural,$page,$qtnbypage);
        }   
    }
?>
