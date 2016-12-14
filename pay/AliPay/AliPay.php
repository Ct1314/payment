<?php
namespace Pay\AliPay;

use Pay\Exception\PayException;
use Pay\Interfaces\PayInterface;
use Pay\AliPay\AliPayHandle;
class AliPay implements PayInterface
{
    /**
     *  pay request parameters
     * @var array
     */
    private $pay_info;

    /**
     * order parameters
     * @var
     */
    public $order_info;

    /**
     * config parameters
     * @var array
     */
    public $parameter = [];

    /**
     * submit pay
     * @param array $order_info
     * @throws PayException
     */
    public function pay(array $order_info)
    {
        if(!$order_info) {
            throw new PayException('Order Parameters NullException');
        }
        // 设置订单值
        $this->initOrderInfo($order_info);
        $this->initParameter();
        $this->request();
    }

    /**
     * init parameters
     */
    private function initParameter()
    {
        $this->pay_info ['service']             = $this->parameter['service'];
        $this->pay_info ['partner']             = $this->parameter['partner'];
        $this->pay_info ['seller_id']           = $this->parameter['seller_id'];
        $this->pay_info ['payment_type']        = $this->parameter['payment_type'];
        $this->pay_info ['notify_url']          = $this->parameter['notify_url'];
        $this->pay_info ['return_url']          = $this->parameter['return_url'];
        $this->pay_info ['anti_phishing_key']   = isset($this->parameter['anti_phishing_key'])?$this->parameter['anti_phishing_key']:'';
        $this->pay_info ['exter_invoke_ip']     = isset($this->parameter['exter_invoke_ip'])?$this->parameter['exter_invoke_ip']:'';
        $this->pay_info ['out_trade_no']        = $this->order_info['out_trade_no'];
        $this->pay_info ['subject']             = $this->order_info['subject'];
        $this->pay_info ['total_fee']           = $this->order_info['total_fee'];
        $this->pay_info ['body']                = $this->order_info['body'];
        $this->pay_info ['_input_charset']      = $this->parameter['_input_charset'];
    }

    /**
     * init order parameters
     * @param array $order_info
     */
    private function initOrderInfo(array $order_info)
    {
        $this->order_info = $order_info;
    }

    /**
     * establish pay request
     */
    public function request()
    {
        $createRequestUrl = $this->createRequestUrl();

        $this->AliPayRequestHandle($createRequestUrl);
    }

    /**
     * filter null parameters
     * @param array $para
     * @return array
     */
    private function filterPara(array $para)
    {
        $temp_para = [];
        foreach($para as $k =>$v ) {
            if(empty($v)) continue;
            $temp_para [$k] = $v;
        }
        return $temp_para;
    }

    /**
     * parameters sort
     * @param $para
     * @return mixed
     */
    public function parametersSort($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }

    /**
     * 生成url链接
     * @return string
     */
    public function createUrl($para)
    {
        $requestUrl = "";
        foreach($para as $key => $value) {
            $requestUrl .=  $key . "=" .$value ."&";
        }
        $requestReplaceUrl = rtrim($requestUrl,"&");
        return $requestReplaceUrl;
    }

    /**
     * create request url
     * @return string
     */
    public function createRequestUrl()
    {
        /*use filter method to filter parameters null value*/
        $para = $this->filterPara($this->pay_info);

        /*with sort parameters*/
        $para = $this->parametersSort($para);

        /*create sign url*/
        $url = $this->createUrl($para);

        /* generate sign  */
        $sign = $this->sign($url);

        $para ['sign']      = $sign;
        $createRequestUrl = $this->parameter['requestUrl'].$url.'&sign='.$sign.'&sign_type='.$this->parameter['sign_type'];
        return $createRequestUrl;
    }


    /**
     * with url md5 encrypt
     * @param $param
     * @return string
     */
    public function sign($param)
    {

        $sign = "";
        if( strtoupper( trim($this->parameter['sign_type']))== "MD5" ) {
            $sign .= md5($param.$this->parameter['key']);
        }
        return $sign;
    }
    /**
     * pay request handle
     * @param $para
     */
    public function AliPayRequestHandle($para)
    {
        $http = new AliPayHandle();

        /* set request content */
        $http->setRequestContent($para);

        /* establish request */
        $http->call();
    }

    /**
     * set multiple parameters
     * @param $params
     */
    public function setAllConfig(array $params)
    {
        if (is_array($params) ) {
            foreach($params as $key => $param) {
                $this->parameter[$key] = $param;
            }
        }
    }

    /**
     * set one parameters
     * @param $param
     */
    public function setConfig($param)
    {
       list($key,$value) = [key($param),current($param)];
        $this->parameter [$key] = $value;
    }

    /**
     * 取得订单支付状态
     */
    public function getPayStatus()
    {

    }

    /**
     * 支付服务器异步通知
     */
    public function verifyNotify()
    {

    }

    /**
     * 支付结果服务器同步跳转通知
     */
    public function verifyReturn()
    {

    }
}