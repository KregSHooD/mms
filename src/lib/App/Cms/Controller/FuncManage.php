<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 2017/2/15
 * Time: 11:34
 */

namespace App\Cms\Controller;

use App\Cms\Model\TemplateFunc;
use CK\Core\Controller;
use CK\Util\Cipher;
use CK\Util\IO\Directory;

/**
 * 模板方法操作类
 * Class FuncManage
 *
 * @res true
 * @package App\Cms\Controller
 */
class FuncManage extends Controller {

    /**
     * 查询模板方法集
     * @param $tmp_id
     * @param $query
     * @param $num
     * @param $page
     *
     * @res true
     * @return bool|array
     */
    public function query($tmp_id,$query,$page,$num) {
        $tmp_id = Cipher::inst()->decrypt($tmp_id);
        if (empty($tmp_id)) {
            return false;
        }

        $fields = [
            'fun_id'=>'',
            'tmp_id'=>'',
            'fun_name'=>'',
            'fun_class'=>'',
            'fun_var'=>'',
            'fun_params'=>'',
            'created_date'=>''
        ];

        $where = ['tmp_id'=>$tmp_id];

        foreach ($query as $result) {
            if (!empty($result['value'])) {
                $column = $result['name']."[{$result['type']}]";
                $where[$column] = $result['value'];
            }
        }

        $result = TemplateFunc::inst()->query($fields,$where,null,null,$num,$page,function($row){
            $row['cipher_id'] = Cipher::inst()->encrypt($row['fun_id']);
            return $row;
        });
        return $result;
    }

    /**
     * 获取所有发布库类列表
     *
     * @res true
     * @return array
     */
    public function getPublishClassList() {
        $path = APP_LIB_PATH.'App/Cms/Publish/';
        $files = Directory::getDir($path);
        $list = [];
        foreach ($files as $item) {
            $list[] = [
                'label'=>basename($item),
                'value'=>basename($item)
            ];
        }
        return $list;
    }
}