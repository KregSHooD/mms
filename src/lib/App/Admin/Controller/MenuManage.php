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
use CK\Cache\Cache;
use CK\Util\Cipher;
use CK\Core\Controller;

/**
 * 管理端菜单管理
 * Class MenuManage
 * @res true
 * @package App\Res\Controller
 */
class MenuManage extends Controller
{

    /**
     * 获取所有菜单数据
     * @param $query
     * @param $page
     * @param $num
     * @res true
     *
     * @return array
     */
    public function getAll($query, $page, $num)
    {
        $fields = [
            'menu_id' => '',
            'menu_parent' => '',
            'menu_sort' => '',
            'menu_name' => '',
            'menu_text' => '',
            'menu_link' => '',
            'menu_parent_text' => '',
            'created_date' => ''
        ];

//        $joins = [
//            ['t_sys_menu b.menu_id', 't_sys_menu.menu_parent', 'LEFT']
//        ];

        $result = Menu::inst()->query($fields, null, null, null, $num, $page, function ($row) {
            $row['created_date'] = date('Y-m-d H:i:s', $row['created_date']);
            $row['cipher_id'] = Cipher::inst()->encrypt($row['menu_id']);
            return $row;
        });
        //查看原生sql语句
//        echo DBA::inst()->sql_str;
        return $result;
    }

    /**
     * 保存菜单
     * @param $data
     * @res true
     *
     * @return bool
     */
    public function save($data)
    {
        if (empty($data['menu_id'])) {
            unset($data['menu_id']);
            $data['created_date'] = CK_NOW;
            $status = Menu::inst()->insert($data, false);
            self::updateGroup();
        } else {
            $data['menu_id'] = Cipher::inst()->decrypt($data['menu_id']);
            if (!is_numeric($data['menu_id'])) {
                return false;
            }
            $status = Menu::inst()->update($data, ['menu_id' => $data['menu_id']]);
        }
        //删除用户缓存权限
        if ($status) {
            $this->deletePurCache();
        }
        //modified by clake
        return $status;
    }

    /**
     * 删除菜单
     * @param $menu_id
     * @res true
     *
     * @return bool | array
     */
    public function delMenu($menu_id)
    {
        if (empty($menu_id)) {
            return ['msg' => false, 'status' => -1, 'tip' => '参数错误'];
        }
        $fields = [
            'menu_id' => '',
            'menu_parent' => ''
        ];
        //菜单下面有子菜单的不允许删除
        $result = Menu::inst()->query($fields, ['menu_parent' => $menu_id]);
        $rows = $result['data'];
        if (!empty($rows)) {
            return ['msg' => false, 'status' => 0, 'tip' => "该菜单下有子菜单，不能进行删除"];
        }
        $res = Menu::inst()->delete(['menu_id' => $menu_id]);
        //删除用户菜单缓存
        if ($res) {
            $this->deletePurCache();
        }
        // modified by clake
        $tip = $res ? '删除成功' : '删除失败';
        self::updateGroup();
        return ['msg' => $res, 'status' => 1, 'tip' => $tip];
    }

    /**
     * 获取一级菜单名称和id
     * @param $cipher_id
     * @res true
     *
     * @return bool|array
     */
    public function getParentMenu($cipher_id)
    {
        if (empty($menu_id)) {
            $result = Menu::inst()->query(['menu_text label' => '', 'menu_id value' => ''], ['menu_parent' => 0], null, null, 1000, 1);
        } else {
            $menu_id = Cipher::inst()->decrypt($cipher_id);
            if (!is_numeric($menu_id)) {
                return false;
            }
            $result = Menu::inst()->query(['menu_text label' => '', 'menu_id value' => ''], ['menu_parent' => 0, 'menu_id' => "!= $menu_id"], null, null, 1000, 1);
        }
        return $result;
    }

    /**
     * 获得一条菜单数据详情
     * @param $id
     * @res true
     *
     * @return array|bool
     */
    public function getMenu($id)
    {
        $menu_id = Cipher::inst()->decrypt($id);
        if (!is_numeric($menu_id)) {
            return false;
        }
        $info = Menu::inst()->getMenuById($menu_id);
        $info['created_date'] = date('Y-m-d H:i:s', $info['created_date']);
        return $info;
    }

    /**
     * 菜单变动修改admin组权限
     */
    private function updateGroup()
    {
        $result = Menu::inst()->query(['menu_id' => ''], null, null, null, 1000, 1, function($row){
            return $row['menu_id'];
        });
        $pur_str = implode(',', $result['data']);
        Group::inst()->upGrpPur($pur_str);
    }

    private function deletePurCache() {
        $group = Group::inst()->query(['grp_id'=>''],null,null,null,100,1);
        $list = $group['data'];
        foreach ($list as $item) {
            Cache::inst()->delete('user_pur_'.$item['grp_id']);
        }
        Auth::inst()->refreshUser();
    }

}