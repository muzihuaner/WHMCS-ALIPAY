<?php
// 防止任何意外输出
ob_start();

require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';

$gatewayModuleName = 'alipaywhmcs';
$gateway = getGatewayVariables($gatewayModuleName);

if (!$gateway["type"]) {
    http_response_code(400); // 返回400状态
    echo "Module Not Activated"; // 可选：返回错误信息
    exit;
}

// 获取支付宝回调数据
$postData = $_POST;

// 验证签名等操作
// TODO: 实现支付宝签名验证

try {
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
        logTransaction($gateway['name'], $postData, 'Success');
    } else {
        logTransaction($gateway['name'], $postData, 'Failed');
    }

    // 给支付宝返回成功通知
    http_response_code(200);
    echo 'success';
} catch (Exception $e) {
    logTransaction($gateway['name'], $postData, 'Error: ' . $e->getMessage());
    http_response_code(500);
    echo 'error';
}
exit;
?>
