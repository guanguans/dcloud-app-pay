<?php

/*
 * This file is part of the guanguans/dcloud-app-pay.
 *
 * (c) 琯琯 <yzmguanguan@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Guanguans\Alipay;

use Guanguans\Alipay\Aop\AlipayTradeAppPayRequest;
use Guanguans\Alipay\Aop\AopClient;
use Guanguans\Alipay\Exceptions\InvalidArgumentException;
use Guanguans\Alipay\Support\Config;

class Alipay
{
    /**
     * @var string
     */
    protected $gateway = 'https://openapi.alipay.com/gateway.do?charset=UTF-8';

    /**
     * alipay global config params.
     *
     * @var array
     */
    protected $config;

    /**
     * user's config params.
     *
     * @var \Guanguans\Alipay\Support\Config
     */
    protected $user_config;

    /**
     * @var \AopClient
     */
    protected $aop;

    /**
     * @var \AlipayTradeAppPayRequest
     */
    protected $request;

    /**
     * [__construct description].
     *
     * @author guanguans
     *
     * @param array $config [description]
     */
    public function __construct(array $config)
    {
        $this->aop = new AopClient();

        $this->request = new AlipayTradeAppPayRequest();

        $this->user_config = new Config($config);

        if (empty($this->user_config->get('app_id'))) {
            throw new InvalidArgumentException('Missing Config -- [app_id]');
        }

        $this->config = [
            'app_id' => $this->user_config->get('app_id'),
            'method' => '',
            'format' => 'JSON',
            'charset' => 'UTF-8',
            'sign_type' => 'RSA2',
            'version' => '1.0',
            'return_url' => $this->user_config->get('return_url', ''),
            'notify_url' => $this->user_config->get('notify_url', ''),
            'timestamp' => date('Y-m-d H:i:s'),
            'sign' => '',
            'biz_content' => '',
        ];
    }

    /**
     * pay a order.
     *
     * @param array $config_biz
     *
     * @return mixed
     */
    public function pay(array $config_biz)
    {
        // AopClient 处理
        $this->aop->gatewayUrl = $this->gateway;
        $this->aop->appId = $this->user_config->get('app_id');
        $this->aop->rsaPrivateKey = $this->user_config->get('private_key');
        $this->aop->format = $this->config['format'];
        $this->aop->charset = $this->config['charset'];
        $this->aop->signType = $this->config['sign_type'];
        $this->aop->alipayrsaPublicKey = $this->user_config->get('ali_public_key');

        // AlipayTradeAppPayRequest 处理
        $bizcontent = '{"body":"'.$config_biz['body'].'",'
                      .'"subject": "'.$config_biz['subject'].'",'
                      .'"out_trade_no": "'.$config_biz['out_trade_no'].'",'
                      .'"timeout_express": "30m",'
                      .'"total_amount": "'.$config_biz['total_amount'].'",'
                      .'"product_code":"QUICK_MSECURITY_PAY"'
                      .'}';
        $this->request->setNotifyUrl($this->user_config->get('notify_url'));
        $this->request->setBizContent($bizcontent);

        // 这里不需要使用 htmlspecialchars 进行转义，直接返回即可
        return $this->aop->sdkExecute($this->request);
    }
}
