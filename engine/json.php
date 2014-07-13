<?php 
    /**
    * Class to manipulate json responses. 
    */
    class json
    {
        private $data,$msg,$status;
        private $success = 'success'; //What should the class return in case of success?
        private $fail    = 'fail';    //What should the class return in case of fail?
        private $statusKey = 'status';//What is the key that holds the status? 
        private $dataKey = 'data';//What is the key that holds the data params? 

        private $msgKey = 'msg';   //What is the key that holds the msg? 
        private function parse($msg,$status,$data=array()){
            if (!is_array($data)) {
                throw new Exception("Error Processing Request, é esperado um array no primeiro parametro da função parse.", 1);                
            }
            $return = array($this->dataKey=>$data);
            $return[$this->statusKey] = $status;
            $return[$this->msgKey]    = $msg;
            return json_encode($return);
        }
        public function success($msg="Requisição executada com sucessso", $data=array()){
            return $this->parse($msg,$this->success,$data);
        }
        public function fail($msg="Requisição falhou", $data=array()){
            return $this->parse($msg,$this->fail,$data);
        }
        public function getInfo(){
            return array(
                "statusKey"=>$this->statusKey,
                "dataKey"  =>$this->dataKey,
                "msgKey"   =>$this->msgKey,
                "fail"     =>$this->fail,
                "success"  =>$this->success,
            );
        }
    }
?>