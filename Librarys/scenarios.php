<?php 
/**
* Class to create different scenarios that return different data-sets based in porcentages
*/
class scenarios
{
	protected $context; //for what you are creating this scenarios? Context examples: Site, Landing Page 1, Landing Page 2.
	protected $scenarios = array();
	function __construct($context)
	{
		$this->context = $context;
	}
	function registerScenario($name,$data,$chance=1){
		$this->scenarios[$name] = array(
			"name"=>$name,
			"data" => $data,
			"chance" => $chance
		);
		return true;
	}
	function getScenario(){
		$sget = data::get("scenario");
		if ($sget and isset($this->scenarios[$sget])) {
			setcookie("Scenarios".$this->context,serialize($this->scenarios[$sget]),time()+120);
			return $this->scenarios[$sget];
		}
		if (isset($_COOKIE["Scenarios".$this->context])) {
			return unserialize($_COOKIE["Scenarios".$this->context]);
		}
		$scenarios = array();
		foreach($this->scenarios as $name => $scenario){
			for ($i=0; $i < $scenario['chance']; $i++) { 
				$scenarios[] = $scenario;
			}
		}
		$r = array_rand($scenarios);
		setcookie("Scenarios".$this->context,serialize($scenarios[$r]),time()+3600*24*30);
		return $scenarios[$r];
	}
	function getScenarioName(){
		$sget = data::get("scenario");
		if ($sget and isset($this->scenarios[$sget])) {
			return $sget;
		}
		if (isset($_COOKIE["Scenarios".$this->context])) {
			$s = unserialize($_COOKIE["Scenarios".$this->context]);
			return $s['name'];
		}
		return "Unknown";
	}
}
?>