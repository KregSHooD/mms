<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 16/11/1
 * Time: 16:49
 */

namespace Common\Component;


use CK\Core\Component;

class Sign extends Component{

    private $_key = 'kg93jf8kkgje863ikjgle03';

    public function sign($data) {
        $data['key'] = $this->_key;
        $values = array_values($data);
        sort($values,SORT_STRING);
        return sha1(join(',',$values));
    }

    public function check($data,$sign) {
        $tmp_sign = $this->sign($data);
        return $tmp_sign === $sign;
    }
}