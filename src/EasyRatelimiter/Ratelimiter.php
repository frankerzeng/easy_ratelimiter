<?php

namespace EasyRatelimiter;

use EasyRatelimiter\shmop\ShmopOperate;


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

    /**限制时间和次数
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
            throw new \Exception("illegal parameter");
        }
        $this->limitTime  = $config['time'];
        $this->limitTimes = $config['times'];
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
                    throw new \Exception("Can't get the user's IP address");
                }
                break;
            case "session":
                $this->limitObj = session_id();
                if (empty($this->limitObj)) {
                    throw new \Exception("use session_start() to start session first");
                }
                break;
            default:
                $this->limitObj = $config['type'];
                break;
        }
    }

    /**
     * 是否超过限制
     * 维护一个map，过期后自动删除
     * @return bool
     * @throws \Exception
     */
    public function check()
    {
        $return = true;
        $info   = trim((new ShmopOperate())->get());

        if (!empty($info) and $info != "") {
            $info = json_decode($info, true);
            $this->deleteExpire($info);

            if (isset($info[$this->limitObj])) {
                if (count($info[$this->limitObj]['t']) < $this->limitTimes) {
                    $info[$this->limitObj]['t'][] = microtime(true);
                } else {
                    $return = false; // 只有次数达到最大才返回false
                }
            } else {
                $info[$this->limitObj] = [
                    'lts' => $this->limitTimes,
                    'lt'  => $this->limitTime,
                    't'   => [microtime(true)],
                ];
            }
        } else {
            $info[$this->limitObj] = [
                'lts' => $this->limitTimes,
                'lt'  => $this->limitTime,
                't'   => [microtime(true)],
            ];
        }

        (new ShmopOperate())->set(json_encode($info));

        return $return;
    }

    /**
     * 删除过期数据
     * @param array $info
     */
    public function deleteExpire(array &$info)
    {
        $rst  = [];
        $time = time();
        foreach ($info as $itemKey => $item) {
            $tmp = [];
            foreach ($item['t'] as $it) {
                if ($time < ($it + $item['lt'])) {
                    $tmp[] = $it;
                }
            }
            if (!empty($tmp)) {
                $rst[$itemKey] = [
                    'lts' => $item['lts'],
                    'lt'  => $item['lt'],
                    't'   => $tmp,
                ];
            }
        }
        $info = $rst;
    }

}
