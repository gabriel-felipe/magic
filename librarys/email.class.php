<?php
	class email {
		public $mensagem,$assunto,$emaildestino,$nomeorigem,$emailorigem,$redireciona;
        public $alerta = false;
		public function send(){
            //MAIS - CONFIGURAÇOES DA MENSAGEM ORIGINAL
            $nome = $this->nomeorigem;
            $email = $this->emailorigem;
            $cabecalho_da_mensagem_original  = "MIME-Version: 1.1\n";
            $cabecalho_da_mensagem_original .= "Content-type:text/html; charset=iso-8859-1<br />";
            $cabecalho_da_mensagem_original .= "From:$nome<$email><br />";
            $cabecalho_da_mensagem_original .= "Return-Path:$nome <$email><br />";  
            //ENVIO DA MENSAGEM ORIGINAL
            $headers = "$cabecalho_da_mensagem_original";
            if(mail($this->emaildestino,$this->assunto,$this->mensagem, $headers, "-f ".$this->emailorigem)){
                if($this->alerta){
                echo "<script>
                alert('".$this->alerta."');
                </script>";
                }
                if($this->redireciona){
                    echo "<script>window.location=\"".$this->redireciona."\";</script>";
                }
                return true;
        	}  else {
                return false;
        	}
		}
	}
?>