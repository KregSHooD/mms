<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 15/6/26
 * Time: 00:49
 */

namespace App\Admin\Model;


use CK\Core\Model;

class Menu extends Model{

    private $_fields = [
        'acc_id'=>'',
        'grp_id'=>'',
        'acc_name'=>'',
        ''=>''
    ];

    public function __construct() {
        parent::__construct('t_sys_menu');
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

    /**
     * 通过菜单id获取一条菜单数据
     * @param $menu_id
     * @return array
     */
    public function getMenuById($menu_id) {
        $this->_table->clear();
        $result = $this->_table
            ->where(['menu_id'=>$menu_id])
            ->execute()->
            get_result_one();
        return $result;
    }

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
}