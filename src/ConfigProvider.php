<?php

declare(strict_types = 1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace Firstphp\HyperfWechat;

use Firstphp\HyperfWechat\WechatInterface;
use Firstphp\HyperfWechat\Facades\WechatFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                WechatInterface::class => WechatFactory::class
            ],
            'commands' => [
            ],
            'scan' => [
                'paths' => [
                    __DIR__,
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for hyperf-wechat.',
                    'source' => __DIR__ . '/publish/wechat.php',
                    'destination' => BASE_PATH . '/config/autoload/wechat.php',
                ],
            ],
        ];
    }
}
