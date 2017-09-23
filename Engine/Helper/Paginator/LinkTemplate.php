<?php 
namespace Magic\Engine\Helper\Paginator;
class LinkTemplate extends AbstractPaginator
{
	protected $linkTemplate=null;
    protected $query = "[p]";
    function __construct($linkTemplate){
        $this->setLinkTemplate($linkTemplate);
    }
    function getLink($page){
        return str_replace($this->getQuery(),$page,$this->linkTemplate);
    }
    function setQuery($query)  {
        $this->query = $query;
        return $this;
    }
    function getQuery(){
        return $this->query;
    }
    function setLinkTemplate($linkTemplate){
        $this->linkTemplate = $linkTemplate;
        return $this;
    }
    function getLinkTemplate(){
        return $this->linkTemplate;
    }
}
?>