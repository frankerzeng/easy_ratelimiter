<?php

namespace Tests;

require_once __DIR__ . '/../vendor/autoload.php';

use EasyRatelimiter\Ratelimiter;
use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: frankerzeng@163.com
 * Date: 2019/6/21
 * Time: 11:10
 */
class ValidateTest extends TestCase
{

    /**
     * @throws \Exception
     */
    public function testIsMobile()
    {
        $validate = (new Ratelimiter(['type' => "17665544332", 'times' => 2, 'time' => 10]))->check();
        $this->assertEquals(true, $validate);
        $validate = (new Ratelimiter(['type' => "17665544332", 'times' => 2, 'time' => 10]))->check();
        $this->assertEquals(true, $validate);
        $validate = (new Ratelimiter(['type' => "17665544332", 'times' => 2, 'time' => 10]))->check();
        $this->assertEquals(false, $validate);

        $validate = (new Ratelimiter(['type' => "frankerzeng@163.com", 'times' => 1, 'time' => 10]))->check();
        $this->assertEquals(true, $validate);
        $validate = (new Ratelimiter(['type' => "frankerzeng@163.com", 'times' => 1, 'time' => 10]))->check();
        $this->assertEquals(false, $validate);

    }
}

