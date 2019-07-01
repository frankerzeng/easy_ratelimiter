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

    public $shmid = null;
    public $systemid = 0;
    public $permissions = 0777;
    public $mode = "c"; // Access mode
    public $size = 1024;

    public $shmids = [];

    /**
     * ShmopOperate constructor.
     * @param string $mode        Access mode
     *                            //模式 “a”，它允许您访问只读内存段
     *                            //模式 “w”，它允许您访问可读写的内存段
     *                            //模式 “c”，它创建一个新内存段，或者如果该内存段已存在，尝试打开它进行读写
     *                            //模式 “n”，它创建一个新内存段，如果该内存段已存在，则会失败
     * @param int    $permissions Permissions for the shared memory segment
     * @param int    $size        Size, in bytes, of the segment
     * @throws ShmopOperateException
     */
    public function __construct($mode = "c", $permissions = 0755, $size = 1024)
    {
        $this->permissions = $permissions;
        $this->mode        = $mode;
        $this->size        = $size;
        $this->systemid    = ftok(__FILE__, "z"); // System ID for the shared memory segment
    }

    public function set($data = '')
    {
        $this->delete();
        $size = mb_strlen($data, 'UTF-8');
        $this->shmid = shmop_open($this->systemid, $this->mode, $this->permissions, $size);
        $result      = shmop_write($this->shmid, $data, 0);
        shmop_close($this->shmid);

        if (!$result) {
            throw new ShmopOperateException("shmop_write error");
        }
    }

    public function delete()
    {
        $shmid = @shmop_open($this->systemid, $this->mode, $this->permissions, 1);
        if ($shmid) {
            shmop_delete($shmid);
            shmop_close($shmid);
        }
    }

    public function get()
    {
        $this->shmid = @shmop_open($this->systemid, $this->mode, $this->permissions, 1);
        if (!$this->shmid) {
            return '';
        }

//        $semid = sem_get($this->shmid); # 请求信号控制权
//        if (sem_acquire($semid)) {
//
//        }

        $rst = shmop_read($this->shmid, 0, shmop_size($this->shmid));
        shmop_close($this->shmid);
        return $rst;
    }


}