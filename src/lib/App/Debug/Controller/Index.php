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
use CK\Util\Cipher;

class Index extends Controller {
    public function acIndex() {
        $cipher = Cipher::inst('mms.zcxf.com')->encrypt("12");
        echo $cipher.'<br/>';

        echo Cipher::inst('mms.zcxf.com')->decrypt($cipher);
    }

    public function acUpdate() {
        $flag = Model::inst('t_account')->update(['modified_date'=>'1'],['acc_id'=>'1']);
        var_dump($flag);
    }
}