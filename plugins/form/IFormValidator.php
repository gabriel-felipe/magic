<?php 
/**
* Interface para gerenciamento de elementos do form.
*/
interface IFormValidator
{
	function getErrorMsg();
	function isValid($value);
}
?>