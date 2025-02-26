# WHMCS 支付宝支付插件

## 目录结构 
```
modules/
├── gateways/
│ ├── alipaywhmcs.php # 主模块文件
│ ├── alipay-whmcs/
│ │ └── config.php # 配置文件
│ └── callback/
│ └── alipay_callback.php # 回调处理文件
```

## 安装步骤
开始前需要在
https://b.alipay.com/page/product-mall/all-product
签约“当面付”和“电脑网站支付”

1. **文件部署**
   - 将 `modules` 目录下的所有文件复制到 WHMCS 根目录对应位置
   - 确保文件权限正确（建议 755）

2. **获取支付宝配置**
   - 登录支付宝开放平台（https://open.alipay.com）
   - 创建应用并获取：
     * 应用ID（APPID）
     * 商户私钥（RSA2私钥）
     * 支付宝公钥

3. **WHMCS后台配置**
   - 登录WHMCS管理后台
   - 进入 `设置` -> `支付网关`
   - 找到 "支付宝支付" 并激活
   - 填写配置信息：
     * 支付宝应用ID
     * 商户私钥
     * 支付宝公钥
   - 测试环境请勾选"测试模式"

4. **配置回调地址**
   - 异步通知地址(Notify URL)：
     ```
     https://你的域名/modules/gateways/callback/alipay_callback.php
     ```

## 使用说明

### 测试环境
1. 开启测试模式
2. 使用支付宝沙箱账号测试
3. 验证支付流程和订单状态

### 正式环境
1. 关闭测试模式
2. 使用正式支付宝账号测试
3. 确认回调正常工作

## 注意事项

1. **系统要求**
   - PHP 7.0+
   - WHMCS 7.0+
   - OpenSSL 扩展

2. **安全建议**
   - 使用HTTPS协议
   - 妥善保管私钥
   - 定期检查日志
   - 开启WHMCS两步验证

3. **常见问题**
   - 订单状态未更新
     * 检查回调地址配置
     * 检查服务器防火墙
     * 查看系统日志
   - 签名验证失败
     * 确认密钥格式正确
     * 验证密钥对应关系

## 技术支持

如遇问题，请检查：
1. WHMCS系统日志
2. 支付宝交易记录
3. 服务器错误日志
4. 配置参数正确性

## 更新记录

- v1.0.0 (2025-02-25)
  * 初始版本发布
  * 支持支付宝PC网页支付
  * 支持异步通知
