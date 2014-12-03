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
    private static function getNumberDependedString($number, $titles, $printNumber = true){
        $absNumber = abs($number);
        $cases = array (2, 0, 1, 1, 1, 2);
        $form = $titles[ ($absNumber % 100 > 4 && $absNumber % 100 < 20) ?  2 : $cases[min($absNumber % 10, 5)] ];
        return ($printNumber ? $number . ' ' : '') . $form;
    }

    /**
     * Возвращает дополнительную информацию по компаниям доставки
     * @return array
     */
    static public function getCompanySubInfo(){
        return array(
                    1 => array('name' => 'PickPoint', 'ico' => 'pickpoint'),
                    3 => array('name' => 'Logibox', 'ico' => 'logibox'),
                    4 => array('name' => 'Boxberry', 'ico' => 'boxberry'),
                    6 => array('name' => 'СДЭК забор', 'ico' => 'cdek'),
                    7 => array('name' => 'QIWI Post', 'ico' => 'qiwi'),
                    11 => array('name' => 'Hermes', 'ico' => 'hermes'),
                    13 => array('name' => 'КТС', 'ico' => 'pack'),
                    14 => array('name' => 'Maxima Express', 'ico' => 'pack'),
                    16 => array('name' => 'IMLogistics Пушкинская', 'ico' => 'imlogistics'),
                    17 => array('name' => 'IMLogistics', 'ico' => 'imlogistics'),
                    18 => array('name' => 'Сам Заберу', 'ico' => 'pack'),
                    20 => array('name' => 'DPD Parcel', 'ico' => 'dpd'),
                    21 => array('name' => 'Boxberry Express', 'ico' => 'boxberry'),
                    22 => array('name' => 'IMLogistics Экспресс', 'ico' => 'imlogistics'),
                    23 => array('name' => 'DPD Consumer', 'ico' => 'dpd'),
                    24 => array('name' => 'Сити Курьер', 'ico' => 'pack'),
                    25 => array('name' => 'СДЭК Посылка Самовывоз', 'ico' => 'cdek'),
                    26 => array('name' => 'СДЭК Посылка до двери', 'ico' => 'cdek'),
                    27 => array('name' => 'DPD ECONOMY', 'ico' => 'dpd'),
                    28 => array('name' => 'DPD Express', 'ico' => 'dpd'),
                    29 => array('name' => 'DPD Classic', 'ico' => 'dpd'),
                    30 => array('name' => 'EMS', 'ico' => 'ems'),
                    31 => array('name' => 'Grastin', 'ico' => 'grastin'),
                    33 => array('name' => 'Aplix', 'ico' => 'aplix'),
                    34 => array('name' => 'Lenod', 'ico' => 'pack'),
                    35 => array('name' => 'Aplix DPD Consum er', 'ico' => 'aplix_dpd_black'),
                    36 => array('name' => 'Aplix DPD parcel', 'ico' => 'aplix_dpd_black'),
                    37 => array('name' => 'Aplix IML самовывоз', 'ico' => 'aplix_imlogistics'),
                    38 => array('name' => 'Aplix PickPoint', 'ico' => 'aplix_pickpoint'),
                    39 => array('name' => 'Aplix Qiwi', 'ico' => 'aplix_qiwi'),
                    40 => array('name' => 'Aplix СДЭК', 'ico' => 'aplix_cdek'),
                    41 => array('name' => 'Кит', 'ico' => 'kit'),
                    42 => array('name' => 'Imlogistics', 'ico' => 'imlogistics'),
                    43 => array('name' => 'Imlogistics', 'ico' => 'imlogistics'),
                    44 => array('name' => 'Почта России', 'ico' => 'russianpost'),
                    45 => array('name' => 'Aplix курьерская доставка', 'ico' => 'aplix'),
                    46 => array('name' => 'Lenod', 'ico' => 'pack'),
                    48 => array('name' => 'Aplix IML курьерская доставка', 'ico' => 'aplix_imlogistics'),
                    49 => array('name' => 'IML Забор', 'ico' => 'imlogistics'),
                    50 => array('name' => 'Почта России 1-й класс', 'ico' => 'mail'),
                    51 => array('name' => 'EMS Почта России', 'ico' => 'ems'),
                    52 => array('name' => 'ЕКБ-доставка забор', 'ico' => 'pack'),
                    53 => array('name' => 'Грейт Экспресс', 'ico' => 'pack'),
                    54 => array('name' => 'Почта России 1-й класс.', 'ico' => 'mail'),
                    55 => array('name' => 'Почта России.', 'ico' => 'mail'),
                    58 => array('name' => 'FSD - курьерская доставка по Москве', 'ico' => 'pack'),
                    61 => array('name' => 'EMS Почта России', 'ico' => 'ems')
        );
    }

    /**
     * Возвращает ID почтових компаний доставкиc
     * @return array
     */
    public static function getPostCompanies(){
        return array(44,50,54,55,51,61);
    }

} 