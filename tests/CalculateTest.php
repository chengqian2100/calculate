<?php

namespace Chengqian2100\Calculate\Test;

use Chengqian2100\Calculate\Calculate;
use DivisionByZeroError;
use PHPUnit\Framework\TestCase;

class CalculateTest extends TestCase
{

    /**
     * 测试加法
     *
     * @return void
     */
    public function testAdd()
    {
        $calc = new Calculate();
        $result = $calc->calc('98 + 9 + 24');
        $result2 = 98 + 9 + 24;
        $this->assertEquals(
            $result,
            $result2
        );
    }

    /**
     * 判断报错
     *
     * @return void
     */
    public function testExec()
    {
        $calc = new Calculate();
        try {
            $result = $calc->calc('98 / (1-1)');
        } catch (DivisionByZeroError $e) {
            $this->assertEquals('ok', 'ok');
        }
        $result2 = 98 + 9 + 24;
    }

    /**
     * 测试复杂的四则运算
     *
     * @return void
     */
    public function testMult()
    {
        $calc = new Calculate();
        $result = $calc->calc('(65 + 98 - (93 + -8) / 90)  *     2.1 + 98.5');
        $result2 = (65 + 98 - (93 + -8) / 90)  *     2.1 + 98.5;
        $this->assertEquals($result, $result2);
    }
}
