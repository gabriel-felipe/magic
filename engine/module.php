<?php

class module
{
  private $registry;
      public function __construct($registry){
         $this->registry = $registry;
      }
      public function __get($key) {
           return $this->registry->get($key);
      }
      public function __call($name, $arguments){
           // Note: value of $name is case sensitive.
           return call_user_func_array(array($this->page,$name),$arguments);
       
      }
}
?>
