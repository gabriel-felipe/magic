<?php 
	/**
	* Classe responsável pela paginação
	*/
	class paginacao
	{
		protected $controller,$obj,$url;
		public $template="common/_paginacao",$urlHolder="%255Bp%255D";
		function __construct(Controller $controller,DbModelPlural &$objs,$urlSample)
		{
			$this->controller = $controller;
			$this->obj = $objs;
			$this->url = $urlSample;
		}

		function getHtml(){
			$t = $this->obj->total;
			$p = $this->obj->nowinpage;
			$qtn = $this->obj->qtnbypage;
			$totalPages = ceil($t/$qtn);
			$pages = array();
			for ($i=1; $i <= $totalPages; $i++) { 
				$pages[$i] = array(
					"number" => $i,
					"link" => str_replace($this->urlHolder, $i, $this->url),
					"active" => (boolean)($p == $i)
				);
			}
			$previous = ($p != 1) ? str_replace($this->urlHolder, 1, $this->url) : false;
			$next = ($p != $totalPages) ? str_replace($this->urlHolder, $totalPages, $this->url) : false;
			$data = array(
				"previous" => $previous,
				"next" => $next,
				"pages"=> $pages
			);
			return $this->controller->get_view($this->template,$data);
		}
	}
?>