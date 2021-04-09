<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 15/6/26
 * Time: 00:49
 */

namespace App\Admin\Model;


use CK\Core\Model;

class Group extends Model{

    public function __construct() {
        parent::__construct('t_account_group');
    }

//    public function
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
     * 通过分组id获得详情
     * @param $grp_id
     *
     * @return array
     */
    public function getGroupBuId($grp_id) {
        $this->_table->clear();
        $result = $this->_table
            ->where(['grp_id'=>$grp_id])
            ->execute()->
            get_result_one();
        return $result;
    }

    /**
     * 修改admin组权限
     * @param $pur string
     */
    public function upGrpPur($pur){
        $this->_table->clear();
        $this->_table->where(['grp_id' => 1])->update(['grp_pur' => $pur]);
    }

    /**
     * 返回所有组数据
     * @return array
     */
    public function getAll() {
        $data = $this->query([
            'grp_id value'=>'',
            'grp_name label'=>''
        ]);
        return $data;
    }
}