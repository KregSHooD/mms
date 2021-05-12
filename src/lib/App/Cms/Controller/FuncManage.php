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
use Monolog\Formatter\ChromePHPFormatter;

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
            'fun_function'=>'',
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
                'label'=>basename($item,'.php'),
                'value'=>basename($item,'.php')
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
                'val'=>$param->isDefaultValueAvailable()?$param->getDefaultValue():'',
                'type'=>''
            ];
        }

        return $list;
    }

    /**
     * 保存模板调用函数方法
     * @param $data
     * @param $tmp_id
     *
     * @res true
     * @return bool
     */
    public function save($data,$tmp_id) {
        $tmp_id = Cipher::inst()->decrypt($tmp_id);
        if (empty($tmp_id)) {
            return false;
        }
        if (empty($data['fun_id'])) {
            unset($data['fun_id']);
            $data['tmp_id'] = $tmp_id;
            $data['created_date'] = CK_NOW;
            $data['modified_date'] = CK_NOW;
            $flag = TemplateFunc::inst()->insert($data,false);
            return $flag;
        } else {
            $data['modified_date'] = CK_NOW;
            $flag = TemplateFunc::inst()->update($data,['tmp_id'=>$data['tmp_id']]);
            return $flag;
        }
    }

    /**
     * 获取调用函数信息
     * @param $cipher_id
     *
     * @res true
     * @return array|bool
     */
    public function info($cipher_id) {
        $fun_id = Cipher::inst()->decrypt($cipher_id);
        if (!empty($fun_id)) {
            $data = TemplateFunc::inst()->find(['fun_id'=>$fun_id]);
            return $data;
        } else {
            return false;
        }
    }
}