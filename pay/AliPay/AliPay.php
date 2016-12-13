<?php
namespace Pay\AliPay;

use Pay\Exception\PayException;
use Pay\Interfaces\PayInterface;
use Pay\HttpClient;
class AliPay implements PayInterface
{
    /**
     *  请求信息参数
     * @var array
     */
    private $pay_info;

    /**
     * 订单信息
     * @var
     */
    public $order_info;

    /**
     * 支付信息参数
     * @var array
     */
    public $parameter = [];

    public function __construct()
    {
    }

    /**
     * 订单支付
     * @param array $order_info
     * @throws PayException
     */
    public function pay(array $order_info)
    {
        // 检测订单是否存在
        if(!$order_info) {
            throw new PayException('NullException');
        }
        // 设置订单值
        $this->initOrderInfo($order_info);
        $this->initParameter();
        echo $this->request();
    }

    /**
     * 初始化请求参数
     */
    private function initParameter()
    {
        /**
         * 构造请求的参数数组
         */
        $this->pay_info ['service']         = $this->parameter['service'];
        $this->pay_info ['partner']         = $this->parameter['partner'];
        $this->pay_info ['seller_id']       = $this->parameter['seller_id'];
        $this->pay_info ['payment_type']    = $this->parameter['payment_type'];
        $this->pay_info ['notify_url']      = $this->parameter['notify_url'];
        $this->pay_info ['return_url']      = $this->parameter['return_url'];
        $this->pay_info ['anti_phishing_key'] = isset($this->parameter['anti_phishing_key'])?$this->parameter['anti_phishing_key']:'';
        $this->pay_info ['exter_invoke_ip'] = isset($this->parameter['exter_invoke_ip'])?$this->parameter['exter_invoke_ip']:'';
        $this->pay_info ['out_trade_no']    = $this->order_info['out_trade_no'];
        $this->pay_info ['subject']         = $this->order_info['subject'];
        $this->pay_info ['total_fee']       = $this->order_info['total_fee'];
        $this->pay_info ['body']            = $this->order_info['body'];
        $this->pay_info ['_input_charset']  = $this->parameter['_input_charset'];
    }

    /**
     * 过滤空值
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
     * 初始化订单信息
     * @param array $order_info
     */
    private function initOrderInfo(array $order_info)
    {
        $this->order_info = $order_info;
    }

    /**
     * 设置多个参数
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
     * 设置单个参数
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

    /**
     * 加签验证
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


    public function createRequestUrl($url)
    {
        $sign = $this->sign($url);

        $para ['sign']      = $sign;
        $createRequestUrl = $this->parameter['requestUrl'].$url.'&sign='.$sign.'&sign_type='.$this->parameter['sign_type'];
//        var_dump($createRequestUrl);
        return $createRequestUrl;
    }

    /**
     * 排序参数
     * @param $para
     * @return mixed
     */
    public function argumentSort($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }

    /**
     * 发起请求
     */
    public function request()
    {
        // 过滤空值
        $para = $this->filterPara($this->pay_info);
        // 对参数排序
        $para = $this->argumentSort($para);
        // 生成url
        $url = $this->createUrl($para);
        $createRequestUrl = $this->createRequestUrl($url);
        $this->http($createRequestUrl);
//        // 生成签名
//        $sign = $this->sign($url);
//
//        $para ['sign']      = $sign;
//        $para ['sign_type'] = $this->parameter['sign_type'];
//        $html = '';
//        $html.="<form id='alipaysubmit' action='".$this->parameter['requestUrl'].'_input_charset='.$this->pay_info['_input_charset']."' method='post' >";
//        foreach ($para as $key=>$value) {
//            $html .= "<input type='hidden' name='".$key."' value='".$value."'>";
//        }
//        $html.="<input type='submit' style='display: none'>";
//        $html.="<form>";
//        $html.="<script>document.forms['alipaysubmit'].submit();</script>";
//        return $html;
    }

    public function http($para)
    {
        $http = new HttpClient();
        $http->setRequestUrl($this->parameter['requestUrl'].'_input_charset='.$this->pay_info['_input_charset']);
        $http->setRequestContent($para);
        $http->call();
        echo $http->getErrorInfo();
    }
}