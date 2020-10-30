<?php

declare(strict_types = 1);

namespace Firstphp\HyperfWechat\Facades;


use Firstphp\HyperfWechat\WechatClient;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;


class WechatFactory
{

    public function __invoke(ContainerInterface $container)
    {
        $contents = $container->get(ConfigInterface::class);
        $config = $contents->get("wechat");
        return $container->make(WechatClient::class, compact('config'));
    }

}

