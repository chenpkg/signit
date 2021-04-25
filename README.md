<h1 align="center"> signit </h1>

<p align="center">易企签 php sdk</p>


## 安装

```shell
$ composer require chenpkg/signit -vvv
```

## 使用

```php
require './vendor/autoload.php';

//use 
use Signit\Factory;

$config = [
    'client_id' => 'xxxx',
    'client_secret' => 'xxxxx',
    
    // 可选
    'base_url' => 'https://open.signit.cn/v1/open'
];

$app = new Factory($config);

$data = [
    //...
];

// 快捷签署
$app->envelopes->quick($data);

// 发起签署流程
$app->envelopes->start($data);

$name = '张三';
$idCardNo = '510113...';
$phone = '18888888888';
$bankNumber = '65123154...';
$customTag = mt_rand(1000, 9999);

// 手机三网认证
$app->auth->phone($name, $idCardNo, $phone, $customTag);

// 银行卡四要素认证
$app->auth->bank($name, $idCardNo, $phone, $bankNumber, $customTag);
```

### 缓存替换
```php
use Symfony\Component\Cache\Adapter\RedisAdapter;

// laravel
$cache = new RedisAdapter(app('redis')->connection()->client());
$app->rebind('cache', $cache);

// redis client
$app->rebind('cache', new RedisAdapter(new \Redis()));
```

## License

MIT