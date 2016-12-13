<?php
namespace Pay;

use Pay\WxPay\WxPay;
use Pay\AliPay\AliPay;
use Pay\Exception\PayException;
class PayFactory
{
    protected $alipay;
    protected $wxpay;
    public static function  create($pay_code,$pay_info = array(),$order_info = array())
    {
        if(!$pay_code) {
            throw new PayException($pay_code."Not Found");
        }
        if($pay_code == 'alipay') {
            return new AliPay($order_info);
        }
        if($pay_code == 'wxpay') {
            return new WxPay($order_info);
        }
    }
}