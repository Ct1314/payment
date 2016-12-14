<?php
namespace Pay\AliPay;
use Pay\RequestHandle;
class AliPayHandle extends RequestHandle
{
    protected function RequestPara()
    {
        $this->realPara = explode('?',$this->requestContent);
        $this->realPara[0] = $this->realPara[0].'?_input_charset=utf-8';
        return $this->realPara;
    }
    public function call()
    {
        parent::call();
    }
}