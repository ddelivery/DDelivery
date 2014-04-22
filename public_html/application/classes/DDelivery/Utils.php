<?php
/**
 * User: DnAp
 * Date: 22.04.14
 * Time: 10:57
 */

namespace DDelivery;


class Utils {
    /**
     * Преобразует первый символ строки в верхний регистр
     * @param $string
     * @return string
     */
    public static function firstWordLiterUppercase($string) {
        $words = array();
        foreach(explode(' ', $string) as $word){
            $words[] =  mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8') .
                        mb_substr($word, 1, mb_strlen($word, 'UTF-8'), 'UTF-8');
        }
        return implode(' ', $words);
    }

    /**
     * Работает так plural($num, 'коммент', 'коммента', 'комментов', 'Нет комментов')
     * @param $count
     * @param $form1
     * @param string $form2
     * @param string $form3
     * @param string $nullForm
     * @param bool $printNumber
     * @return string
     */
    public static function plural($count, $form1, $form2 = null, $form3 = null, $nullForm = null, $printNumber = true)
    {
        if(!$count && !is_null($nullForm)){
            return $nullForm;
        }
        $form2 = is_null($form2) ? $form1 : $form2;
        $form3 = is_null($form3) ? $form2 : $form3;

        return self::getNumberDependedString($count, array($form1, $form2, $form3), $printNumber);
    }

    /**
     * Formats the number with strings
     *
     * @param int $number
     * @param array $titles
     * @param bool $printNumber
     * @return string
     */
    private static function getNumberDependedString($number, $titles, $printNumber = true)
    {
        $absNumber = abs($number);
        $cases = array (2, 0, 1, 1, 1, 2);
        $form = $titles[ ($absNumber % 100 > 4 && $absNumber % 100 < 20) ?  2 : $cases[min($absNumber % 10, 5)] ];
        return ($printNumber ? $number . ' ' : '') . $form;
    }

} 