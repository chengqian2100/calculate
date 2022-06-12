<?php

namespace Chengqian2100\Calculate;

use DivisionByZeroError;

/**
 * @author chengqian2100 <chengqian2100@139.com>
 */
class Calculate
{
    /**
     * 计算式子
     *
     * @var string
     */
    private string $formula;

    public function calc(string $formula): float
    {

        $this->formula = $formula;
        $formulaArr = $this->parseFormula($formula);
        $formulaAfter = $this->trasformFormula($formulaArr);
        $resultNum = $this->calcFormula($formulaAfter);
        return $resultNum;
    }

    /**
     * 将字符串解析成计算式子
     *
     * @param string $formula
     * @return array<string|float>
     */
    private function parseFormula(string $formula): array
    {
        $formulaArr = str_split($formula);
        $formulaTmp = [];
        $numTmp = '';
        $num = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.'];
        $opr = ['(', '+', '*', '/', ')'];
        $minus = ['-'];
        $isMinus = true;
        foreach ($formulaArr as $formulaOne) {
            $formulaOne = strval($formulaOne);
            if (in_array($formulaOne, $num, true)) {
                $numTmp .= $formulaOne;
                $isMinus = false;
            } elseif (in_array($formulaOne, $opr, true)) {
                if ($numTmp !== '') {
                    array_push($formulaTmp, floatval($numTmp));
                    $numTmp = '';
                }
                if (in_array($formulaOne, [')'], true)) {
                    $isMinus = false;
                } else {
                    $isMinus = true;
                }
                array_push($formulaTmp, $formulaOne);
            } elseif (in_array($formulaOne, $minus, true)) {
                if ($isMinus) {
                    $numTmp .= '-';
                } else {
                    if ($numTmp !== '') {
                        array_push($formulaTmp, floatval($numTmp));
                        $numTmp = '';
                    }
                    array_push($formulaTmp, $formulaOne);
                }
            } elseif ($formulaOne === ' ') {
                continue;
            } else {
                throw new FormulaErrException('表达式有误: ' . $this->formula);
            }
        }
        if (!empty($numTmp)) {
            array_push($formulaTmp, floatval($numTmp));
            $numTmp = '';
        }
        return $formulaTmp;
    }


    /**
     * 将中序计算式，转成后序计算式
     *
     * @param array<float|string> $formulaArr
     * @return array<float|string>
     */
    private function trasformFormula(array $formulaArr): array
    {
        $oprTmp = [];
        $resultTmp = [];
        foreach ($formulaArr as $formulaOne) {
            if (is_numeric($formulaOne)) {
                array_push($resultTmp, $formulaOne);
            } else {
                switch ($formulaOne) {
                    case '+':
                    case '-':
                        while (true) {
                            $preOpr = array_shift($oprTmp);
                            if (is_null($preOpr)) {
                                break;
                            }
                            if (in_array($preOpr, ['+', '-', '*', '/'])) {
                                array_push($resultTmp, $preOpr);
                                continue;
                            } else {
                                array_unshift($oprTmp, $preOpr);
                                break;
                            }
                        }
                        array_unshift($oprTmp, $formulaOne);
                        break;
                    case '*':
                    case '/':
                        while (true) {
                            $preOpr = array_shift($oprTmp);
                            if (is_null($preOpr)) {
                                break;
                            }
                            if (in_array($preOpr, ['*', '/'])) {
                                array_push($resultTmp, $preOpr);
                                continue;
                            } else {
                                array_unshift($oprTmp, $preOpr);
                                break;
                            }
                        }
                        array_unshift($oprTmp, $formulaOne);
                        break;
                    case '(':
                        array_unshift($oprTmp, $formulaOne);
                        break;
                    case ')':
                        while (true) {
                            $preOpr = array_shift($oprTmp);
                            if (is_null($preOpr)) {
                                break;
                            }
                            if ($preOpr === '(') {
                                break;
                            } else {
                                array_push($resultTmp, $preOpr);
                            }
                        }
                        break;
                }
            }
        }
        while (true) {
            $preOpr = array_shift($oprTmp);
            if (is_null($preOpr)) {
                break;
            } else {
                array_push($resultTmp, $preOpr);
            }
        }
        return $resultTmp;
    }

    /**
     * 通过后序计算式计算出结果
     *
     * @param array<string|float> $formulaAfter
     * @return float
     */
    private function calcFormula(array $formulaAfter): float
    {
        $resultTmp = [];
        while (true) {
            $opr = array_shift($formulaAfter);
            if (is_null($opr)) {
                break;
            }
            if (is_numeric($opr)) {
                array_push($resultTmp, $opr);
            } else {
                $oprNum2 = array_pop($resultTmp);
                $oprNum1 = array_pop($resultTmp);
                if (is_null($oprNum1) || is_null($oprNum2)) {
                    throw new FormulaErrException('计算公式有误: ' . $this->formula);
                }
                switch ($opr) {
                    case '+':
                        $tmp = $oprNum1 + $oprNum2;
                        array_push($resultTmp, $tmp);
                        break;
                    case '-':
                        $tmp = $oprNum1 - $oprNum2;
                        array_push($resultTmp, $tmp);
                        break;
                    case '*':
                        $tmp = $oprNum1 * $oprNum2;
                        array_push($resultTmp, $tmp);
                        break;
                    case '/':
                        if ($oprNum2 === 0) {
                            throw new DivisionByZeroError('除数为0');
                        }
                        $tmp = $oprNum1 / $oprNum2;
                        array_push($resultTmp, $tmp);
                        break;
                }
            }
        }
        if (count($resultTmp) === 1) {
            return floatval($resultTmp[0]);
        } else {
            throw new FormulaErrException('计算公式有错: ' . $this->formula);
        }
    }
}
