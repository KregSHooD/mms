<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 17/1/13
 * Time: 20:08
 */

namespace App\Debug\Controller;


use CK\Core\Controller;

class Index extends Controller {
    public function acIndex() {
        $str = 'clake[+]';

        $reg = '/(.+?)\[(\+|-|!|>|<)\]/si';
        $flag = preg_match($reg,$str,$match);
        var_dump($flag);
        var_dump($match);
        $field = [
            'column'=>'',
            'icon'=>''
        ];
        $field['column'] = $match[0];
        $field['icon'] = $match[1];
    }
}