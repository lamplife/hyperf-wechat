## 微信公众号开发组件 for hyperf

### 安装组件:
>composer require firstphp/hyperf-wechat

### 发布配置:
>php bin/hyperf.php vendor:publish firstphp/hyperf-wechat

### 编辑.env配置：
```php
WECHAT_APPID=wxba34db123lafdu811
WECHAT_APPSECRET=a09dfwlf9df90b74g4a8l9ca8d67bu7o0
WECHAT_KEY=qpy1DkAVBAZD2L5rs0uiKeoWa62wLumjqCXG9HifL3n
WECHAT_URL=https://api.weixin.qq.com/
AES_KEY=AQm3DkAVBAZD2L1rsOWaKeoRda62wLumjqD9G9HifA1a
```

### 示例代码：
```php
use Firstphp\FirstphpWechat\WechatInterface;

......

/**
 * @Inject
 * @var WechatInterface
 */
protected $wechatInterface;

public function test() {
    $res = $this->wechatInterface->getAccessToken();
    var_dump($res);
}
```