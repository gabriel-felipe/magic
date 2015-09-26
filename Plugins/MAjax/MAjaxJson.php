<?php 
namespace Magic\Plugins\MAjax;
/**
* Class to manipulate json responses. 
*/
class MAjaxJson
{
    protected $data=array();
    protected $msg;
    protected $statusCode=200;
    /**
     * Gets the value of data.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Sets the value of data.
     *
     * @param mixed $data the data
     *
     * @return self
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Gets the value of msg.
     *
     * @return mixed
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /** 
     * 
     */

    /**
     * Sets the value of msg.
     *
     * @param mixed $msg the msg
     *
     * @return self
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;

        return $this;
    }

    /**
     * Gets the value of statusCode.
     *
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Sets the value of statusCode.
     *
     * @param mixed $statusCode the status code
     *
     * @return self
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Gets and sets the code of response.
     *
     * @return mixed
     */
    public function responseCode($code = NULL) {
        return \Magic\Engine\Document\Response::code($code);
    }

    public function set($key,$value){
        $this->data[$key] = $value;
    }

    public function __set($key,$value){
        $this->set($key,$value);
    }

    public function render($echoNDie=1){
        header("content-type: application/json");
        $data = array();
        $data['statusCode'] = $this->getStatusCode();
        $data['msg'] = $this->getMsg();
        $data['data'] = $this->getData();
        $result = json_encode($data,JSON_HEX_QUOT | JSON_HEX_TAG);
        if ($echoNDie) {
            echo $result;
            die();
        } else {
            return $result;
        }
    }

}
?>