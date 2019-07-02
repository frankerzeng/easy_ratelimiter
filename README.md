# php ratelimiter reference Guava Ratelimiter 
基于内存的访问频率限制

作者在项目中用到的地方是登录时限制登录次数，如10分钟内只能重试5次、短信验证码1分钟内最多发一次，24小时内最多发5次等

## Setup/Installation
You can include this library by running:  
`composer require frankerzeng/easy_ratelimiter`

### Example
```php
// 验证10秒内1次访问
$validate = (new Ratelimiter(['type' => "17665544332", 'times' => 1, 'time' => 10]))->check();
var_dump($validate);// bool(true)

$validate = (new Ratelimiter(['type' => "17665544332", 'times' => 1, 'time' => 10]))->check();
var_dump($validate);// bool(false)
```

### Debug
进入服务器控制共享内存
ipcs -m 查看本机共享内存的状态和统计

ipcrm -m shmid 清除共享内存中的数据。

### Notice
- 需要开启shmop拓展

    在Dockerfile中加入RUN docker-php-ext-install shmop
- 并发高的情况需要考虑原子性
    

### TODO
由于共享内存的写操作不是原子性的，在并发大的情况下应该增加并发控制，保证原子性