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
        $validate = Ratelimiter::factory("CN");
        $this->assertEquals(false, $validate->isMobile("176666"));
        $this->assertEquals(true, $validate->isMobile("17665353177"));

        $validate = Ratelimiter::factory("DE");
        $this->assertEquals(false, $validate->isMobile("16"));
        $this->assertEquals(true, $validate->isMobile("01511234567"));
        $this->assertEquals(true, $validate->isMobile("00491511234567"));
        $this->assertEquals(true, $validate->isMobile("1511234567"));

        $rateLimiter = new RateLimiter(['type'=>"ip"]);
        try {
            // allow a maximum of 100 requests for the IP in 5 minutes
            $rateLimiter->limitRequestsInMinutes(100, 5);
        } catch (RateExceededException $e) {
            header("HTTP/1.0 529 Too Many Requests");
            exit;
        }

    }
}

