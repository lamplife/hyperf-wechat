
<?php

declare(strict_types=1);

/**
 * 公众号开发之配置文件
 *
 * User: 狂奔的螞蟻 <www.firstphp.com>
 * Date: 2020/10/29
 * Time: 下午16:48
 */


return [
    /*
    |--------------------------------------------------------------------------
    | AppId
    |--------------------------------------------------------------------------
    */
    'appid' => env('WECHAT_APPID', 'wx086a5e2b45d43478'),

    /*
    |--------------------------------------------------------------------------
    | AppSecret
    |--------------------------------------------------------------------------
    */
    'appsecret' => env('WECHAT_APPSECRET', '02e0279ab8lee8b2d14c08069123843a'),

    /*
    |--------------------------------------------------------------------------
    | Key
    |--------------------------------------------------------------------------
    */
    'token' => env('WECHAT_TOKEN', 'aqpsmw4bglFed7ed'),

    /*
    |--------------------------------------------------------------------------
    | 消息加解密Key
    |--------------------------------------------------------------------------
    */
    'aes_key' => env('WECHAT_AES_KEY', 'aq1dDkAVBAZD2L5rs3QaKeoWa62wLumjqCXG9Hia9oM'),

];
