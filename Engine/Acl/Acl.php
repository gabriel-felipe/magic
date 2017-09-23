<?php 
namespace Magic\Engine\Acl;
use \Exception;

class Acl
{
	protected $resources=array();
	protected $allowed=array();
	function addResource($resource){

		if (!$this->hasResource($resource)) {
			$this->resources[] = $resource;	
		} else {
			throw new \Exception("Resource já incluído na ACL", 1);
		}
		return $this;
	}

	function hasResource($resource){
		if (in_array($resource,$this->resources)) {
			return true;
		}
		return false;
	}

	function allow($resource)
	{
		if (!$this->hasResource($resource)) {
			throw new \Exception("Resource não existe na ACL - $resource", 1);
		}
		if (!in_array($resource, $this->allowed)) {
			$this->allowed[] = $resource;
		}
		return $this;
	}

	function isAllowed($resource){
		if (!$this->hasResource($resource)) {
			throw new \Exception("Resource não existe na ACL - $resource", 1);
		}
		if (in_array($resource, $this->allowed)) {
			return true;
		}
		return false;
	}
}
?>