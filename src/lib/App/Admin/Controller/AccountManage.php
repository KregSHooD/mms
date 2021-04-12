<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 16/10/12
 * Time: 11:56
 */

namespace App\Admin\Controller;


use App\Admin\Common\Auth;
use App\Admin\Model\Group;
use App\Admin\Model\User;
use Common\Component\Upload;
use CK\Core\Controller;

/**
 * 管理端登录用户管理
 * Class AccountManage
 * @res true
 * @package App\Res\Controller
 */
class AccountManage extends Controller{

    /**
     * 得到用户列表
     * @param $query
     * @param $page
     * @param $num
     * @res true
     *
     * @return array
     */
    public function query($query, $page, $num) {
        $fields = [
            'acc_id'=>'',
            'grp_name'=>'',
            'acc_username'=>'',
            'acc_head_img'=>'',
            'acc_name'=>'',
            'acc_email'=>'',
            'created_date'=>'',
            'modified_date'=>''
        ];

        $where = [];

        foreach ($query as $result) {
            if (!empty($result['value'])) {
                $column = $result['name']."[{$result['type']}]";
                $where[$column] = $result['value'];
            }
        }

        $result = User::inst()->query($fields,$where,null,null,$num,$page);
        return $result;
    }

    /**
     * 保存用户资料
     * @param $data
     * @res true
     *
     * @return array
     */
    public function save($data) {
        if ($data['upload_file']) {
            $data['acc_head_img'] = Upload::inst()->update(APP_PATH.substr($data['upload_file'],1));
        }
        unset($data['upload_file']);

        if (!empty($data['acc_password'])) {
            $data['acc_password'] = Auth::inst()->cipherPwd($data['acc_password']);
        } else {
            unset($data['acc_password']);
        }

        if (empty($data['acc_id'])) {
            unset($data['acc_id']);
            $data['create_date'] = CK_NOW;
            $data['modified_date'] = CK_NOW;
            $check_name = User::inst()->find(['acc_username' => $data['acc_username']]);
            if(!empty($check_name)){
                $flag = ['msg' => false, 'tip' => '重复的登录名'];
            }else{
                $status = User::inst()->insert($data,false);
                $tip = $status ? '添加成功' : '添加失败';
                $flag = ['msg' => $status, 'tip' => $tip];
            }
        } else {
            $data['modified_date'] = CK_NOW;
            $status = User::inst()->update($data,['acc_id'=>$data['acc_id']]);
            $tip = $status ? '修改成功' : '修改失败';
            $flag = ['msg' => $status, 'tip' => $tip];
        }

        return $flag;
    }

    /**
     * 用户ID得到用户
     * @param $id
     * @res true
     *
     * @return array|bool
     */
    public function getUser($id) {
        $info = User::inst()->find(['acc_id'=>$id]);
        $info['create_date'] = date('Y-m-d H:i:s',$info['create_date']);
        $info['modified_date'] = date('Y-m-d H:i:s',$info['modified_date']);
        return $info;
    }

    /**
     * 得到用户分组下拉数据
     * @res true
     * @return array
     */
    public function getGroup() {
        return Group::inst()->getAll();
    }

    /**
     * 删除用户结果不能逆转
     * @param $users
     * @res true
     *
     * @return bool
     */
    public function delUser($users) {
        return User::inst()->delete(['acc_id'=>$users]);
    }

    /**
     * 上传图片
     * @param $base
     * @res true
     *
     * @return bool|string
     */
    public function uploadImg($base) {
        $file_path = Upload::inst()->updateBase64($base);
        return $file_path;
    }
}