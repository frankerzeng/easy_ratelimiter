<?php

namespace EasyRatelimiter;

use EasyRatelimiter\shmop\ShmopOperate;
use EasyRatelimiter\shmop\ShmopOperateException;


/**
 * Created by PhpStorm.
 * User: frankerzeng@163.com
 * Date: 2019/6/21
 * Time: 10:59
 */
class RateLimiter
{

    /**
     * 校验规则 ip,sessionid,url,other（other是表示其他的自定义标识，比如限制发送短信给同一个手机号的频率）
     * @var string
     */
    private $limitObj = '';

    /**限制频率和次数
     * @var int
     */
    private $limitTime, $limitTimes = 0;

    /**
     * RateLimiter constructor.
     * @param array $config
     *                array{
     *                type:校验规则,ip,session,url,other（other是表示其他的自定义标识，比如限制发送短信给同一个手机号的频率）
     *                time:时间内
     *                times:次数
     *                }
     * @throws \Exception
     */
    public function __construct($config = [])
    {
        if (empty($config['type']) || empty($config['time']) || empty($config['times'])) {
            throw new ShmopOperateException("illegal parameter");
        }
        switch ($config['type']) {
            case "ip":
                $ip = $_SERVER['REMOTE_ADDR'];
                if (isset($_SERVER['HTTP_CDN_SRC_IP'])) {
                    $ip = $_SERVER['HTTP_CDN_SRC_IP'];
                } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
                }
                if ($ip2long = ip2long($ip)) {
                    $this->limitObj = sprintf('%u', $ip2long);
                } else {
                    throw new ShmopOperateException("Can't get the user's IP address");
                }
                break;
            case "session":
                $this->limitObj = session_id();
                if (empty($this->limitObj)) {
                    throw new ShmopOperateException("use session_start() to start session first");
                }
                break;
            default:
                $this->limitObj = $config['type'];
                break;
        }
    }

    /**
     * 是否超过限制
     * @return bool
     * @throws \Exception
     */
    public function request()
    {
        $obj = new ShmopOperate("23345345");
        var_dump($obj->get());
        return;
    }
}
