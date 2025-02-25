<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// 支付宝网关
define('ALIPAY_GATEWAY_URL', 'https://openapi.alipay.com/gateway.do');
define('ALIPAY_SANDBOX_GATEWAY_URL', 'https://openapi.alipaydev.com/gateway.do');

// 支付宝接口名称
define('ALIPAY_METHOD', 'alipay.trade.page.pay');
define('ALIPAY_VERSION', '1.0');
define('ALIPAY_CHARSET', 'utf-8');
define('ALIPAY_SIGN_TYPE', 'RSA2'); 