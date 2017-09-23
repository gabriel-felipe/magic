<?php 
namespace Magic\Engine\Helper\Paginator;
abstract class AbstractPaginator
{
	protected $totalPages=0;
	protected $page=0;
	protected $showing=0;
	protected $view=null;
	protected $totalItens=0;
	protected $qtnByPage=1;



    /**
     * Gets the value of totalPages.
     *
     * @return mixed
     */
    public function getTotalPages()
    {
        $totalPages = ceil($this->getTotalItens()/$this->getQtnByPage());
        return $totalPages;
    }

    /**
     * Gets the value of totalItens.
     *
     * @return mixed
     */
    public function getTotalItens()
    {
        return $this->totalItens;
    }

    /**
     * Sets the value of totalItens.
     *
     * @param mixed $totalItens the total Itens
     *
     * @return self
     */
    public function setTotalItens($totalItens)
    {
        $this->totalItens = $totalItens;

        return $this;
    }

    /**
     * Gets the value of page.
     *
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Sets the value of page.
     *
     * @param mixed $page the page
     *
     * @return self
     */
    public function setPage($page)
    {
    	if ($page > $this->getTotalPages()) {
    		throw new PaginatorException("Can't set page for a number bigger than the total of pages", 1);
    		
    	}
        $this->page = $page;

        return $this;
    }

    /**
     * Gets the value of showing.
     *
     * @return mixed
     */
    public function getShowing()
    {
        return $this->showing;
    }

    /**
     * Sets the value of showing.
     *
     * @param mixed $showing the showing
     *
     * @return self
     */
    public function setShowing($showing)
    {
        $this->showing = $showing;

        return $this;
    }

    /**
     * Gets the value of view.
     *
     * @return mixed
     */
    public function getView()
    {
    	global $registry;
    	if (!$this->view instanceof \Magic\Engine\Mvc\View\AbstractView) {
    		$this->view = new \Magic\Engine\Mvc\View\CommonView("helper/paginator");
    		$registry->get("ViewHandler")->prepare($this->view);
    	}
        return $this->view;
    }

    /**
     * Sets the value of view.
     *
     * @param mixed $view the view
     *
     * @return self
     */
    public function setView($view)
    {
        $this->view = $view;
        return $this;
    }

    abstract function getLink($page);

    public function render(){
    	$view = $this->getView();
    	$pages = array();
    	for ($i=1; $i <= $this->getTotalPages(); $i++) { 
    		$pages[$i] = $this->getLink($i);
    	}
    	$view->pages = $pages;
    	$view->actual = $this->getPage();
    	return $view->render();
    }

    public function __toString(){
    	return $this->render();
    }

    /**
     * Sets the value of qtnByPage.
     *
     * @param mixed $qtnByPage the qtn by page
     *
     * @return self
     */
    public function setQtnByPage($qtnByPage)
    {
        $this->qtnByPage = $qtnByPage;

        return $this;
    }

    public function getQtnByPage(){
    	return $this->qtnByPage;
    }
}
?>