<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once __DIR__ . '/alipay-whmcs/config.php';

function alipaywhmcs_config() {
    return array(
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => '支付宝支付'
        ),
        'appId' => array(
            'FriendlyName' => '支付宝应用ID',
            'Type' => 'text',
            'Size' => '50',
            'Default' => '',
            'Description' => '请输入支付宝开放平台申请的应用ID',
        ),
        'privateKey' => array(
            'FriendlyName' => '商户私钥',
            'Type' => 'textarea',
            'Rows' => '3',
            'Description' => '请输入您的商户私钥',
        ),
        'publicKey' => array(
            'FriendlyName' => '支付宝公钥',
            'Type' => 'textarea',
            'Rows' => '3',
            'Description' => '请输入支付宝提供的公钥',
        ),
        'testMode' => array(
            'FriendlyName' => '测试模式',
            'Type' => 'yesno',
            'Description' => '勾选启用测试模式',
        ),
    );
}

function alipaywhmcs_link($params) {
    // 获取系统参数
    $systemUrl = $params['systemurl'];
    $returnUrl = $params['returnurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $invoiceId = $params['invoiceid'];
    $description = $params["description"];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];

    // 配置参数
    $config = array(
        'app_id' => $params['appId'],
        'private_key' => $params['privateKey'],
        'public_key' => $params['publicKey'],
        'gateway_url' => $params['testMode'] == 'on' ? ALIPAY_SANDBOX_GATEWAY_URL : ALIPAY_GATEWAY_URL
    );

    // 构建支付参数
    $payRequestBuilder = array(
        'app_id' => $config['app_id'],
        'method' => ALIPAY_METHOD,
        'format' => 'JSON',
        'charset' => ALIPAY_CHARSET,
        'sign_type' => ALIPAY_SIGN_TYPE,
        'timestamp' => date('Y-m-d H:i:s'),
        'version' => ALIPAY_VERSION,
        'notify_url' => $systemUrl . '/modules/gateways/callback/alipay_callback.php',
        'return_url' => $returnUrl,
        'biz_content' => json_encode(array(
            'out_trade_no' => $invoiceId,
            'total_amount' => $amount,
            'subject' => $description,
            'product_code' => 'FAST_INSTANT_TRADE_PAY'
        ))
    );

    // 生成签名
    $payRequestBuilder['sign'] = generateAlipaySign($payRequestBuilder, $config['private_key']);

    // 构建支付表单
    $htmlOutput = '<form id="alipaysubmit" name="alipaysubmit" action="' . $config['gateway_url'] . '" method="POST">';
    foreach ($payRequestBuilder as $key => $value) {
        $htmlOutput .= '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($value) . '"/>';
    }
    $htmlOutput .= '<input type="submit" value="' . $langPayNow . '" style="display:none;"/>';
    $htmlOutput .= '</form>';
    $htmlOutput .= '<script>document.forms["alipaysubmit"].submit();</script>';

    return $htmlOutput;
}

function generateAlipaySign($params, $privateKey) {
    ksort($params);
    $stringToBeSigned = "";
    foreach ($params as $k => $v) {
        if ($v !== '' && $v !== null && !is_array($v)) {
            $stringToBeSigned .= "&{$k}={$v}";
        }
    }
    $stringToBeSigned = substr($stringToBeSigned, 1);
    
    $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n" .
        wordwrap($privateKey, 64, "\n", true) .
        "\n-----END RSA PRIVATE KEY-----";
    
    $key = openssl_get_privatekey($privateKey);
    openssl_sign($stringToBeSigned, $sign, $key, OPENSSL_ALGO_SHA256);
    openssl_free_key($key);
    
    return base64_encode($sign);
} 