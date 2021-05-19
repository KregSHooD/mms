<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 2017/2/13
 * Time: 14:04
 */

namespace App\Cms\Controller;


use App\Cms\Model\Template;
use CK\Core\Controller;
use CK\Database\DBA;
use CK\Util\Cipher;

/**
 * 模板管理类
 * Class TemplateManage
 * @res true
 * @package App\Admin\Controller
 */
class TemplateManage extends Controller {

    /**
     * 查询模板列表
     * @param $query
     * @param $page
     * @param $num
     *
     * @res true
     * @return array
     */
    public function query($query, $page, $num) {
        $fields = [
            'tmp_id'=>'',
            'tmp_name'=>'',
            'tmp_path'=>'',
            'tmp_publish_path'=>'',
            'created_date'=>'',
        ];

        $where = [];

        foreach ($query as $result) {
            if (!empty($result['value'])) {
                $column = $result['name']."[{$result['type']}]";
                $where[$column] = $result['value'];
            }
        }
        $result = Template::inst()->query($fields,$where,null,null,$num,$page,function($row){
            $row['cipher_id'] = Cipher::inst()->encrypt($row['tmp_id']);
            return $row;
        });
        return $result;
    }

    /**
     * 获取所有模板数据
     *
     * @res true
     * @return array
     */
    public function getAll(){
        $fields = [
            'tmp_id value'=>'',
            'tmp_name label'=>'',
        ];
        $result = Template::inst()->query($fields,[],null,null,30,1,function($row){
            return $row;
        });
        return $result;
    }

    /**
     * 保存模板数据
     * @param $data
     *
     * @res true
     * @return bool
     */
    public function save($data) {
        if (empty($data['tmp_id'])) {
            unset($data['tmp_id']);
            $data['created_date'] = CK_NOW;
            $flag = Template::inst()->insert($data);
            return $flag;
        } else {
            $data['modified_date'] = CK_NOW;
            $flag = Template::inst()->update($data,['tmp_id'=>$data['tmp_id']]);
            return $flag;
        }
    }

    /**
     * 获取模板信息
     * @param $cipher_id
     *
     * @res true
     * @return array|bool
     */
    public function info($cipher_id) {
        $tmp_id = Cipher::inst()->decrypt($cipher_id);
        if (!empty($tmp_id)) {
            $template = Template::inst()->find(['tmp_id'=>$tmp_id]);
            return $template;
        } else {
            return false;
        }
    }
}