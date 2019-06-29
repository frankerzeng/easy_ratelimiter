<?php
/**
 * Created by PhpStorm.
 * User: zengfanlong1
 * Date: 2019/6/26
 * Time: 15:29
 */

namespace EasyRatelimiter\shmop;


class ShmopOperateException extends \Exception
{
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }

    public function __toString()
    {
        return $this->message . "<br>";
    }

}