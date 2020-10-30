<?php

declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: 狂奔的螞蟻 <www.firstphp.com>
 * Date: 2020/10/29
 * Time: 下午16:48
 */

namespace Firstphp\HyperfWechat\Bridge;

use Hyperf\Guzzle\ClientFactory;

class Http
{

    /**
     * @var string
     */
    protected $baseUrl = 'https://api.weixin.qq.com/';


    /**
     * @var object
     */
    protected $client;


    /**
     * Http constructor.
     * @param array $config
     * @param ClientFactory $clientFactory
     */
    public function __construct(array $config = [], ClientFactory $clientFactory)
    {
        $baseUri = isset($config['url']) && $config['url'] ? $config['url'] : $this->baseUrl;
        $options = [
            'base_uri' => $baseUri,
            'timeout' => 2.0,
            'verify' => false,
        ];
        $this->client = $clientFactory->create($options);
    }


    /**
     * @param $name
     * @param $arguments
     * @return string
     */
    public function __call($name, $arguments)
    {
        $response = $this->client->request($name, $arguments[0], $arguments[1])->getBody()->getContents();
        return $response;
    }


}