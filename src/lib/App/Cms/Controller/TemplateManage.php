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

/**
 * 模板管理类
 * Class TemplateManage
 *
 * @package App\Admin\Controller
 */
class TemplateManage extends Controller {

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

        $result = Template::inst()->query($fields,$where,null,null,$num,$page);
        return $result;
    }

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
}