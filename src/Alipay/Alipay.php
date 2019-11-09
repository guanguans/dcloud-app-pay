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
    protected $config = [
        'app_id' => '',
        'method' => '',
        'format' => 'JSON',
        'charset' => 'UTF-8',
        'sign_type' => 'RSA2',
        'version' => '1.0',
        'return_url' => '',
        'notify_url' => '',
        'timestamp' => '',
        'sign' => '',
        'biz_content' => '',
    ];

    /**
     * @var AopClient
     */
    protected $aopClient;

    /**
     * @var AlipayTradeAppPayRequest
     */
    protected $alipayTradeAppPayRequest;

    /**
     * [__construct description].
     *
     * @author guanguans
     *
     * @param array $config [description]
     */
    public function __construct(array $config)
    {
        if (empty($config['app_id'])) {
            throw new InvalidArgumentException('Missing Config -- [app_id]');
        }

        $config['timestamp'] = date('Y-m-d H:i:s');

        $this->setConfig(new Config(array_merge($this->getConfig(), $config)));
        $this->setAopClient(new AopClient());
        $this->setAlipayTradeAppPayRequest(new AlipayTradeAppPayRequest());
    }

    /**
     * @param array $configBiz
     *
     * @return string
     */
    public function pay(array $configBiz)
    {
        // AopClient 配置
        $this->setAopClientConfig($this->getConfig());
        // AlipayTradeAppPayRequest 配置
        $this->setAlipayTradeAppPayRequestConfig($this->getBizContent($configBiz));
        // 这里不需要使用 htmlspecialchars 进行转义，直接返回即可
        return $this->getAopClient()->sdkExecute($this->getAlipayTradeAppPayRequest());
    }

    /**
     * @return string
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param \Guanguans\Alipay\Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return \Guanguans\Alipay\Aop\AopClient
     */
    public function getAopClient()
    {
        return $this->aopClient;
    }

    /**
     * @param \Guanguans\Alipay\Aop\AopClient $aopClient
     */
    public function setAopClient(AopClient $aopClient)
    {
        $this->aopClient = $aopClient;
    }

    /**
     * @param \Guanguans\Alipay\Aop\AopClient $aopClient
     */
    public function setAopClientConfig($aopClientConfig)
    {
        $this->aopClient->gatewayUrl = $this->getGateway();
        $this->aopClient->appId = $aopClientConfig->get('app_id');
        $this->aopClient->rsaPrivateKey = $aopClientConfig->get('private_key');
        $this->aopClient->format = $aopClientConfig->get('format');
        $this->aopClient->charset = $aopClientConfig->get('charset');
        $this->aopClient->signType = $aopClientConfig->get('sign_type');
        $this->aopClient->alipayrsaPublicKey = $aopClientConfig->get('ali_public_key');
    }

    /**
     * @return \Guanguans\Alipay\Aop\AlipayTradeAppPayRequest
     */
    public function getAlipayTradeAppPayRequest()
    {
        return $this->alipayTradeAppPayRequest;
    }

    /**
     * @param \Guanguans\Alipay\Aop\AlipayTradeAppPayRequest $alipayTradeAppPayRequest
     */
    public function setAlipayTradeAppPayRequest(alipayTradeAppPayRequest $alipayTradeAppPayRequest)
    {
        $this->alipayTradeAppPayRequest = $alipayTradeAppPayRequest;
    }

    /**
     * @param $setAlipayTradeAppPayRequestConfig
     */
    public function setAlipayTradeAppPayRequestConfig($setAlipayTradeAppPayRequestConfig)
    {
        $this->alipayTradeAppPayRequest->setNotifyUrl($this->getConfig()->get('notify_url'));
        $this->alipayTradeAppPayRequest->setBizContent($setAlipayTradeAppPayRequestConfig);
    }

    /**
     * @param array $configBiz
     *
     * @return string
     */
    public function getBizContent(array $configBiz)
    {
        return '{"body":"'.$configBiz['body'].'",'
               .'"subject": "'.$configBiz['subject'].'",'
               .'"out_trade_no": "'.$configBiz['out_trade_no'].'",'
               .'"timeout_express": "30m",'
               .'"total_amount": "'.$configBiz['total_amount'].'",'
               .'"product_code":"QUICK_MSECURITY_PAY"'
               .'}';
    }
}
