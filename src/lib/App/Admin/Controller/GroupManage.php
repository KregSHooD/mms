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
use App\Admin\Model\Menu;
use App\Admin\Model\User;
use CK\Cache\Cache;
use CK\Util\Cipher;
use CK\Core\Controller;

/**
 * 管理端分组管理
 * Class GroupManage
 * @res true
 * @package App\Res\Controller
 */
class GroupManage extends Controller {

    /**
     * 获取所有分组
     * @param $page
     * @param $num
     * @return array
     * @res true
     *
     * @return array
     */
    public function getAll($page, $num) {
        $fields = [
            'grp_id' => '',
            'grp_name' => '',
            'grp_eng_name' => '',
            'grp_pur' => '',
            'create_date' => '',
            'modified_date' => ''
        ];
        $result = Group::inst()->query($fields, null, null, ['grp_id', 'DESC'], $num, $page, function ($row) {
            $row['create_date'] = date('Y-m-d H:i:s', $row['create_date']);
            $row['modified_date'] = date('Y-m-d H:i:s', $row['modified_date']);
            $row['cipher_id'] = Cipher::inst()->encrypt($row['grp_id']);
            return $row;
        });
        return $result;
    }

    /**
     * 保存分组
     * @param $data
     * @res true
     *
     * @return bool
     */
    public function save($data) {
        if (empty($data['grp_id'])) {
            unset($data['grp_id']);
            $check_name = Group::inst()->find(['grp_name' => $data['grp_name']]);
            if (!empty($check_name)) {
                return false;
            }
            $data['create_date'] = CK_NOW;
            $data['modified_date'] = CK_NOW;
            $status = Group::inst()->insert($data, false);
        } else {
            $data['grp_id'] = Cipher::inst()->decrypt($data['grp_id']);
            if (!is_numeric($data['grp_id'])) {
                return false;
            }
            $check_name = Group::inst()->find(['grp_name' => $data['grp_name'], 'grp_id' => '!= ' . $data['grp_id']]);
            if (!empty($check_name)) {
                return false;
            }
            $data['modified_date'] = CK_NOW;
            $status = Group::inst()->update($data, ['grp_id' => $data['grp_id']]);
        }
        //删除用户缓存
        if ($status) {
            $this->deletePurCache();
        }
        return ['is_ins' => $status, 'param' => $data];
    }

    /**
     * 删除分组
     * @param $grp_id
     * @res true
     *
     * @return bool
     */
    public function delGroup($grp_id) {
        if (empty($grp_id)) {
            return ['msg' => false, 'status' => -1, 'tip' => "参数错误"];
        }
        if ($grp_id == 1) {
            return ['msg' => false, 'status' => 0, 'tip' => "admin管理组不能进行删除"];
        }
        $check_user = User::inst()->find(['grp_id' => $grp_id]);
        if (!empty($check_user)) {
            return ['msg' => false, 'status' => -2, 'tip' => "该分组下还有用户，不能进行删除"];
        }
        $res = Group::inst()->delete(['grp_id' => $grp_id]);
        $tip = $res ? '删除成功' : '删除失败';
        return ['msg' => $res, 'status' => 1, 'tip' => $tip];
    }

    /**
     * 修改分组信息
     * @param $id
     * @res true
     * @return array
     */
    public function getGroup($id) {
        $grp_id = Cipher::inst()->decrypt($id);
        if (!is_numeric($grp_id)) {
            return false;
        }
        $info = Group::inst()->getGroupBuId($grp_id);
        $info['create_date'] = date('Y-m-d H:i:s', $info['create_date']);
        return $info;
    }

    /**
     * 查询所有分组
     * @res true
     *
     * @return array
     */
    public function getAllPur() {
        $result = Menu::inst()->query(['menu_text' => '', 'menu_id' => ''], ['menu_parent' => 0], null, null, 100, 1);
        $top_menu = $result['data'];
        if (empty($top_menu)) {
            return false;
        }
        foreach ($top_menu as &$v) {
            $sub_menu = Menu::inst()->query(['menu_text' => '', 'menu_id' => ''], ['menu_parent' => $v['menu_id']], null, ['menu_ors', 'DESC'], 100, 1);
            $v['sub_menu'] = $sub_menu['data'];
        }
        return $top_menu;
    }

    /**
     * 得到当前用户的权限菜单
     * @res true
     * @return bool|array
     */
    public function getUserPurMenu() {
        $user = Auth::inst()->getUser();
        $key = 'user_pur_' . $user['grp_id'];
        if ($data = Cache::inst()->get($key)) {
            return $data;
        } else {
            $pur_list = $user['grp_pur'];
            $parent_field = [
                'menu_text text' => '',
                'menu_id id' => ''
            ];

            $child_field = [
                'menu_text text' => '',
                'menu_id id' => '',
                'menu_link link' => ''
            ];

            $result = Menu::inst()->query($parent_field, ['menu_parent' => 0], null, null, 100, 1);

            if (empty($result['data'])) {
                return false;
            }

            $top_menu = [];

            foreach ($result['data'] as $v) {
                if (in_array($v['id'], $pur_list)) {
                    $sub_menu = Menu::inst()->query(
                        $child_field,
                        ['menu_parent' => $v['id']],
                        null,
                        ['menu_ors', 'ASC'],
                        100, 1);
                    $tmp = [];
                    foreach ($sub_menu['data'] as $sub) {
                        if (in_array($sub['id'], $pur_list)) {
                            $tmp[] = $sub;
                        }
                    }
                    $v['children'] = $tmp;
                    $top_menu[] = $v;
                }
            }
            Cache::inst()->set($key, $top_menu);
            return $top_menu;
        }
    }

    private function deletePurCache() {
        $group = Group::inst()->query(['grp_id' => ''], null, null, null, 100, 1);
        $list = $group['data'];
        foreach ($list as $item) {
            Cache::inst()->delete('user_pur_' . $item['grp_id']);
        }
        Auth::inst()->refreshUser();
    }
}