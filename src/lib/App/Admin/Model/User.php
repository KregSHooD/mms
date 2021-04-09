<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 15/6/26
 * Time: 00:49
 */

namespace App\Admin\Model;


use CK\Core\Model;

class User extends Model{

    private $_fields = [
        'acc_id'=>'',
        'grp_id'=>'',
        'acc_name'=>'',
        ''=>''
    ];

    public function __construct() {
        parent::__construct('t_account');
    }

    /**
     * 后台用户ID获取用户信息
     * @param $usr_id
     *
     * @return array
     */
    public function getUserById($usr_id) {
        $this->_table->clear();
        $result = $this->_table
            ->select([
                '*'=>'',
                'grp_pur'=>'t_account_group',
                'grp_name'=>'t_account_group',
                'grp_eng_name'=>'t_account_group'
            ])
            ->join('t_account_group','grp_id','t_account','grp_id')
            ->where(['acc_id'=>$usr_id])
            ->execute()->
            get_result_one();
        return $result;
    }

//    public function getUserByWx($wx_id) {
//        $this->_table->clear();
//        $result = $this->_table
//            ->select([
//                '*'=>'',
//                'grp_pur'=>'t_usr_group',
//                'grp_name'=>'t_usr_group',
//                'grp_eng_name'=>'t_usr_group'
//            ])
//            ->join('t_usr_group','grp_id','t_usr_user','grp_id')
//            ->where(['wx_id'=>$wx_id])
//            ->execute()->
//            get_result_one();
//        return $result;
//    }

    /**
     * 查询用户名和密码
     * @param $username
     * @param $password
     *
     * @return array
     */
    public function queryUser($username,$password) {
        $this->_table->clear();
        $result = $this->_table
            ->select([
                '*'=>'',
                'grp_pur'=>'t_account_group',
                'grp_name'=>'t_account_group',
                'grp_eng_name'=>'t_account_group'
            ])
            ->join('t_account_group','grp_id','t_account','grp_id')
            ->where(['acc_username'=>$username,'acc_password'=>$password])
            ->execute()->
            get_result_one();
        return $result;
    }

    /**
     * 通过商户id获取一条数据
     * @param $mch_id
     * @return array
     */
    public function getMerchantById($mch_id) {
        $this->_table->clear();
        $result = $this->_table
            ->select(['acc_id' => '', 'acc_name' => ''])
            ->where(['mch_id'=>$mch_id])
            ->execute()->
            get_result_one();
        return $result;
    }
}