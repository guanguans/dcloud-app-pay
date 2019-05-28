# DCloud app 支付 SDK

[![Build Status](https://travis-ci.org/guanguans/dcloud-app-pay.svg?branch=master)](https://travis-ci.org/guanguans/dcloud-app-pay)
[![StyleCI](https://github.styleci.io/repos/159013854/shield?branch=master)](https://github.styleci.io/repos/159013854)
[![Latest Stable Version](https://poser.pugx.org/guanguans/dcloud-app-pay/v/stable)](https://packagist.org/packages/guanguans/dcloud-app-pay)
[![License](https://poser.pugx.org/guanguans/dcloud-app-pay/license)](https://packagist.org/packages/guanguans/dcloud-app-pay)

DCloud App 支付，基于 [H5P.Server/payment](https://github.com/dcloudio/H5P.Server/blob/master/payment) 修改。

## 安装

``` bash
$ composer require guanguans/dcloud-app-pay
```
## 使用

``` php
require_once 'path/vendor/autoload.php';

use Guanguans\Alipay\Alipay;

$config = [
    'app_id'         => '', // 支付宝提供的 APP_ID
    'notify_url'     => '', // 支付宝异步通知地址
    'ali_public_key' => '', // 支付宝公钥，1行填写
    'private_key'    => '', // 自己的私钥，1行填写
];

$alipy = new Alipay($config);

$config_biz = [
    'out_trade_no' => time(),    // 订单号
    'total_amount' => '1',       // 订单金额，单位：元
    'subject'      => 'subject', // 订单商品标题
    'body'         => 'body',    // 订单商品内容
];

echo $alipy->pay($config_biz);
```

## License

[MIT](LICENSE)