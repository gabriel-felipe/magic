<?php 
namespace Magic\Engine\Helper\Paginator\Adapter;
use Magic\Engine\Helper\Paginator as Paginator;

class PaginatorAdapterFactory
{
	static function getClass($class,$params){

		$r = new \ReflectionClass($class);
		$class = $r->newInstanceArgs($params);
		return $class;
	}
	static function getPaginatorAdapter($strategy,array $strategyParams,$adapter,$adapted,$strategyPrefix="Magic\Engine\Helper\Paginator\\",$adapterPrefix="Magic\Engine\Helper\Paginator\Adapter\\")
	{
		$class = $strategyPrefix.$strategy;
		$paginator = self::getClass($class,$strategyParams);

		$class = $adapterPrefix.$adapter;
		$adapter = self::getClass($class,array($paginator,$adapted));
		return $adapter;
	}

	public static function __callStatic($name, $arguments)
    {
    	$matchAdapter = false;
    	$matchPaginator = false;
    	if (preg_match("/^get.+AsA.+$/", $name)) {
    		$classes = substr($name,3);
    		$asAPos = strpos($classes,"AsA");
    		$adapter = substr($classes,0,$asAPos);
    		$paginator = substr($classes,$asAPos+3);
    		$adapterParams = array_slice($arguments, 0,1);
    		$paginatorParams = array_slice($arguments, 1);

    		$adapter = "Magic\Engine\Helper\Paginator\Adapter\\".$adapter;
    		$paginator = "Magic\Engine\Helper\Paginator\\".$paginator;

    		if (class_exists($adapter)) {
    			$matchAdapter = true;
    		}
    		
    		if (class_exists($paginator)) {
    			$matchPaginator = true;
    		}

    		if ($matchAdapter and $matchPaginator) {
    			$paginator = self::getClass($paginator,$paginatorParams);
    			$adapter = self::getClass($adapter,array($paginator,$arguments[0]));
    			return $adapter;
    		} else if (!$matchAdapter and !$matchPaginator){
    			throw new \Exception("Either adapter or paginator were not found please check names.", 1);
    			
    		} else if ($matchAdapter and !$matchPaginator){
    			throw new \Exception("Paginator wasn't found please check name.", 1);
    		} else if (!$matchAdapter and $matchPaginator){
    			throw new \Exception("Adapter wasn't found please check name.", 1);
    		}
    		
    	} else {
    		throw new \Exception("Method $name doesn't exist.", 1);	
    	}
    }
}

?>