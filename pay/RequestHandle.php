<?php

namespace Pay;
class RequestHandle
{
    /**
     * 请求信息
     * @var $requestContent
     */
    public $requestContent;

    /**
     * 响应信息
     * @var $responseContent
     */
    public $responseContent;

    /**
     * 请求方法
     * @var $method
     */
    public $method = 'POST';

    /**
     * cer文件
     * @var $cerFile
     */
    public $cerFile;

    /**
     * cer密码
     * @var $cerPassword
     */
    public $cerPassword;

    /**
     * cer类型
     * @var $cerType
     */
    public $cerType;

    /**
     * ca文件
     * @var $caFile
     */
    public $caFile;

    /**
     * 超时时间
     * @var $timeOut
     */
    public $timeOut;

    /**
     * 错误信息
     * @var $errorInfo
     */
    public $errorInfo;

    /**
     * 响应http协议号
     * @var $responseHttpCode
     */
    public $response;

    public $requestUrl;

    protected $requestPara;

    protected $realPara;
    public function __construct()
    {
        $this->initHttpClient();
    }

    protected function initHttpClient()
    {
        $this->requestContent   = "";
        $this->requestContent   = "";
        $this->method           = "POST";
        $this->cerFile          = "";
        $this->cerPassword      = "";
        $this->cerType          = "PEM";
        $this->timeOut          = 120;
        $this->errorInfo        = "";
        $this->responseHttpCode = 0;
    }
    public function setRequestUrl($url)
    {
        $this->requestUrl = $url;
    }

    /**
     * setRequestContent
     * @param $requestContent
     */
    public function setRequestContent($requestContent)
    {
        $this->requestContent = $requestContent;
    }

    /**
     * getRequestContent
     * @return requestContent
     * @access public
     */
    public function getRequestContent()
    {
        return $this->requestContent;
    }

    /**
     * setResponseContent
     * @param $responseContent
     * @access public
     */
    public function setResponseContent($responseContent)
    {
        $this->responseContent = $responseContent;
    }

    /**
     * getResponseContent
     * @return responseContent
     * @access public
     */
    public function getResponseContent()
    {
        return $this->responseContent;
    }

    /**
     * setMethod
     * @param $method
     * @access public
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * setCerInfo
     * @param $cerFile
     * @param $cerPassword
     * @param string $cerType
     * @access public
     */
    public function setCerInfo($cerFile,$cerPassword,$cerType = 'PEM')
    {
        $this->cerFile      = $cerFile;
        $this->cerPassword  = $cerPassword;
        $this->cerType      = $cerType;
    }

    /**
     * setCaInfo
     * @param $caFile
     */
    public function serCaInfo($caFile)
    {
        $this->caFile = $caFile;
    }

    protected function RequestPara()
    {
        $this->realPara = explode('?',$this->responseContent);
    }
    protected function call()
    {
        // 启动会话
        $ch = curl_init();
        // 设置超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$this->timeOut);
        // 设置返回形式
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        // 检查ssl加密算法是否存在
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
        // 设置请求类型
       $this->RequestPara();
        // 设置请求url
        if(count($this->realPara)>=2 && $this->method = 'POST') {
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_URL,$this->realPara[0]);
        // 设置请求数据
            curl_setopt($ch,CURLOPT_POSTFIELDS,$this->realPara[1]);
        }else {
            // get请求
            curl_setopt($ch,CURLOPT_URL,$this->requestContent);
        }
        // 设置证书信息
        if($this->cerFile !="") {
            curl_setopt($ch,CURLOPT_SSLCERT,$this->cerFile);
            curl_setopt($ch,CURLOPT_SSLCERTPASSWD,$this->cerPassword);
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,$this->cerType);
        }
        // 设置CA
        if($this->caFile !="") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_CAINFO, $this->caFile);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }
        // 执行
        curl_exec($ch);
        $this->response = curl_getinfo($ch);
        curl_close($ch);
        if($this->response['http_code'] == 301 || $this->response['http_code'] == 302) {
            $this->redirect($this->response['redirect_url']);
        }
        return true;
    }
    public function redirect($url)
    {
        Header('Location: '.$url);
    }
    /**
     * getErrorInfo
     * @return errorInfo
     * @access public
     */
    public function getErrorInfo()
    {
        return $this->errorInfo;
    }
}