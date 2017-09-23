<?php 
namespace Magic\Engine\Helper\Paginator;
class Router extends AbstractPaginator
{
	protected $route=null;
    protected $scope=false ;
    protected $adicionalParameters = array();
    protected $parameterName="page";
    
    function __construct($route,$scope=false,$adicionalParameters=array()){
        $this->setRoute($route);
        $this->setScope($scope);
        $this->setAdicionalParameters($adicionalParameters);
    }

    /**
     * Gets the value of route.
     *
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Sets the value of route.
     *
     * @param mixed $route the route
     *
     * @return self
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Gets the value of scope.
     *
     * @return mixed
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Sets the value of scope.
     *
     * @param mixed $scope the scope
     *
     * @return self
     */
    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Gets the value of adicionalParameters.
     *
     * @return mixed
     */
    public function getAdicionalParameters()
    {
        return $this->adicionalParameters;
    }

    /**
     * Sets the value of adicionalParameters.
     *
     * @param mixed $adicionalParameters the adicional parameters
     *
     * @return self
     */
    public function setAdicionalParameters($adicionalParameters)
    {
        $this->adicionalParameters = $adicionalParameters;

        return $this;
    }

    /**
     * Gets the value of parameterName.
     *
     * @return mixed
     */
    public function getParameterName()
    {
        return $this->parameterName;
    }

    /**
     * Sets the value of parameterName.
     *
     * @param mixed $parameterName the parameter name
     *
     * @return self
     */
    public function setParameterName($parameterName)
    {
        $this->parameterName = $parameterName;

        return $this;
    }

    function getLink($page){
        $url = \Magic\Engine\Mvc\Url::getInstance();
        $data = array();
        $data[$this->getParameterName()] = $page;
        $parameters = array_merge($this->getAdicionalParameters(),$data);
        return $url->get($this->getRoute(),$parameters,$this->getScope());
    }
}
?>