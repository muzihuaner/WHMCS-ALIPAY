<?php

require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';

$gatewayModuleName = 'alipaywhmcs';
$gateway = getGatewayVariables($gatewayModuleName);

if (!$gateway["type"]) {
    die("Module Not Activated");
}

// 获取支付宝回调数据
$postData = $_POST;

// 验证签名等操作
// TODO: 实现支付宝签名验证

// 获取订单信息
$invoiceId = checkCbInvoiceID($postData['out_trade_no'], $gateway['name']);
$transactionId = $postData['trade_no'];
$paymentAmount = $postData['total_amount'];
$paymentSuccess = ($postData['trade_status'] === 'TRADE_SUCCESS');

if ($paymentSuccess) {
    // 添加支付记录
    addInvoicePayment(
        $invoiceId,
        $transactionId,
        $paymentAmount,
        0,
        $gatewayModuleName
    );
    
    // 记录成功交易
    logTransaction($gateway['name'], $postData, 'Success');
} else {
    // 记录失败交易
    logTransaction($gateway['name'], $postData, 'Failed');
} 