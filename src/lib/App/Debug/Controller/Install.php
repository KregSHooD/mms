<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 17/1/16
 * Time: 11:25
 */

namespace App\Debug\Controller;


use App\Admin\Common\Auth;
use CK\Core\Controller;
use CK\Core\Model;

class Install extends Controller {
    public function acInstall() {
        $group = [
            'grp_id'=>'1',
            'grp_name'=>'管理员',
            'grp_eng_name'=>'admin',
            'grp_pur'=>'',
            'created_date'=>CK_NOW,
            'modified_date'=>CK_NOW
        ];

        $user = [
            'acc_id'=>'1',
            'grp_id'=>'1',
            'grp_name'=>'超级管理员',
            'acc_pur'=>'',
            'acc_name'=>'管理员',
            'acc_username'=>'admin',
            'acc_password'=>Auth::inst()->cipherPwd('123123'),
            'acc_head_img'=>'',
            'acc_email'=>'zcxf@zcxf.com',
            'acc_status'=>'1',
            'created_date'=>CK_NOW,
            'modified_date'=>CK_NOW
        ];

        $flag = Model::inst('t_account_group')->insert($group);
        if ($flag) {
            echo "完成用户组初始化!<br/>";

            $flag = Model::inst('t_account')->insert($user);
            if ($flag) {
                echo "完成超级用户初始化!<br/>";
            }
        }
    }
}