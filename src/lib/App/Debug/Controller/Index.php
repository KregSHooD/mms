<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 17/1/13
 * Time: 20:08
 */

namespace App\Debug\Controller;


use CK\Core\Controller;
use CK\Core\Model;
use CK\Database\DBA;

class Index extends Controller {
    public function acIndex() {
        $data = Model::inst('classify')->find(['id'=>'1']);
        var_dump($data);
        echo DBA::inst()->sql_str;
    }
}