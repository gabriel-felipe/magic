<?php
	/**
	* 
	*/
	class WebServiceHelper
	{
		
		public function doPostRequest($url, $dados, $optional_headers = null)
		{
		     // Aqui entra o action do formulário - pra onde os dados serão enviados
				$cURL = curl_init($url);
				curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);

				// Iremos usar o método POST
				curl_setopt($cURL, CURLOPT_POST, true);
				// Definimos quais informações serão enviadas pelo POST (array)
				curl_setopt($cURL, CURLOPT_POSTFIELDS, $dados);

				$resultado = curl_exec($cURL);
				curl_close($cURL);
				return $resultado;
		};

	}
?>