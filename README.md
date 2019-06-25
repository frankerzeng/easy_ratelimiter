# php ratelimiter reference Guava Ratelimiter 
基于内存的访问频率限制

作者在项目中用到的地方是登录时限制登录次数，如10分钟内只能重试5次、短信验证码1分钟内最多发一次，24小时内最多发5次

## Setup/Installation
You can include this library by running:  
`composer require frankerzeng/easy_ratelimiter`

### Example
```php
$isMobile=Validate::factory("CN")->isMobile("17634342323");
var_dump($isMobile);// bool(true)
```