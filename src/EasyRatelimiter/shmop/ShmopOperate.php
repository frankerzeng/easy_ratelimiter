<?php
/**
 * Created by PhpStorm.
 * 共享内存操作
 * User: zengfanlong1
 * Date: 2019/6/26
 * Time: 15:26
 */

namespace EasyRatelimiter\shmop;


class ShmopOperate
{

    private $shmid = null;

    /**
     * ShmopOperate constructor.
     * @param        $systemid    System ID for the shared memory segment
     * @param string $mode        Access mode
     *                            //模式 “a”，它允许您访问只读内存段
     *                            //模式 “w”，它允许您访问可读写的内存段
     *                            //模式 “c”，它创建一个新内存段，或者如果该内存段已存在，尝试打开它进行读写
     *                            //模式 “n”，它创建一个新内存段，如果该内存段已存在，则会失败
     * @param int    $permissions Permissions for the shared memory segment
     * @param int    $size        Size, in bytes, of the segment
     * @throws ShmopOperateException
     */
    public function __construct($systemid, $mode = "c", $permissions = 0755, $size = 1024)
    {
        if (empty($systemid)) {
            $systemid = $id = ftok(__FILE__, "b");
        }

        $this->shmid = shmop_open($systemid, $mode, $permissions, $size);
        if (!$this->shmid) {
            throw new ShmopOperateException("shmop_open error");
        }
    }

    public function __destruct()
    {
        shmop_close($this->shmid);
    }

    public function set($data)
    {
        $result = shmop_write($this->shmid, "Hello World!", 0);
        if (!$result) {
            throw new ShmopOperateException("shmop_write error");
        }
    }

    public function get()
    {
        $size = shmop_size($this->shmid);
        return shmop_read($this->shmid, 0, $size);
    }

    public function del()
    {
        shmop_delete($this->shmid);
    }


}