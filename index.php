<?php
require __DIR__ ."/vendor/autoload.php";
$pay = \Pay\PayFactory::create('alipay');
$params = [
    'requestUrl'=>'https://mapi.alipay.com/gateway.do?',
    'partner'=>'2088901067169902',
    'seller_id'=>'2088901067169902',
    'key'=>'oqc0sbpwfp2gvkmcrcmxbp6d5z5j489t',
    'notify_url'=>'http://商户网址/create_direct_pay_by_user-PHP-UTF-8/notify_url.php',
    'return_url'=>'http://商户网址/create_direct_pay_by_user-PHP-UTF-8/return_url.php',
    'sign_type'=>'MD5',
    '_input_charset'=>'utf-8',
    'cacert'=> getcwd().'\\cacert.pem',
    'transport'=>'http',
    'payment_type'=>"1",
    'service'=>"create_direct_pay_by_user",
];
$pay->setAllConfig($params);
$order = [
    'body'=>'即时到账测试',
    'total_fee'=>'0.01',
    'subject'=>'test商品123',
    'out_trade_no'=>'test20161205143988',
];
$pay->pay($order);