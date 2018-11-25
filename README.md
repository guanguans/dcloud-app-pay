# dcloud-app-alipay

DCloud 支付宝 App 支付，基于 [H5P.Server/payment/alipayrsa2](https://github.com/dcloudio/H5P.Server/blob/master/payment/alipayrsa2/README.md) 修改。

## 安装

``` bash
composer require guanguans/dcloud-app-alipay
```
## 使用

``` php
require_once '../vendor/autoload.php';

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