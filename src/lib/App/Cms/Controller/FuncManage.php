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
    private $_ns;

    public function __construct() {
        $this->_ns = CK_TOP_NS."\\Cms\\Publish\\";
    }

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

    /**
     * 得到类库的公共方法集
     * @param $class_name
     *
     * @res true
     * @return array
     */
    public function getPublishFuncs($class_name) {
        $ref = new \ReflectionClass($this->_ns.$class_name);
        $func_list = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
        $list = [];
        foreach ($func_list as $func) {
            $list[] = [
                'label'=>$func->getName(),
                'value'=>$func->getName()
            ];
        }

        return $list;
    }

    /**
     * 得到方法的所有参数集
     * @param $class_name
     * @param $func_name
     *
     * @res true
     * @return array
     */
    public function getFuncParams($class_name,$func_name) {
        $ref = new \ReflectionClass($this->_ns.$class_name);
        $func = $ref->getMethod($func_name);
        $params = $func->getParameters();
        $list = [];
        foreach ($params as $param) {
            $list[] = [
                'name'=>$param->getName(),
                'def'=>$param->getDefaultValue()
            ];
        }

        return $list;
    }
}