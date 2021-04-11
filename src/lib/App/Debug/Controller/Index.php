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
        $result = Model::inst('t_account')->find(['acc_id'=>'1']);
        var_dump($result);
    }

    public function acUpdate() {
        $flag = Model::inst('t_account')->update(['modified_date[+]'=>'1'],['acc_id'=>'1']);
        var_dump($flag);
    }
}