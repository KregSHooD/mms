<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 16/10/11
 * Time: 23:56
 */

namespace App\Cms;


use CK\Core\Router;
use CK\Util\IO\File;

/**
 * Content Management System 远程调用模块类
 * Cms RPC Class Module
 *
 * @package App\Res
 */
class Module extends \CK\Core\Module{
    private $namespace;


    public function run() {
        $this->namespace = CK_TOP_NS.'\\Cms\\Controller';
        if (CK_DEBUG) {
            header('Access-Control-Allow-Origin:http://localhosœt:3000');
            header('Access-Control-Allow-Methods:GET,POST,OPTIONS');
            header('Access-Control-Allow-Headers:Origin, No-Cache, X-Requested-With, If-Modified-Since, Pragma, Last-Modified, Cache-Control, Expires, Content-Type, X-E4M-With');
            header('Access-Control-Allow-Credentials:true');
        }
        if (CK_IS_POST) {
            //获取所有POST参数
            $raw = file_get_contents("php://input");
            $post = json_decode($raw,true);
            
            $class = under2hump(Router::inst()->getUrlParams(1));
            $func = under2hump(Router::inst()->getUrlParams(2));

            $class_name = $this->namespace.'\\'.$class;

            $rel_class = $this->checkClass($class);

            $rel_method = $this->checkFunc($rel_class, $func);


            $relParams = $rel_method->getParameters();
            $param_names = [];
            $param_default_values = [];
            foreach ($relParams as $param) {
                $param_names[] = $param->name;
                //如果有可选参数
                if ($param->isOptional()) {
                    $param_default_values[$param->name] = $param->getDefaultValue();
                }
            }

            //获取当前实例类
            $class = $class_name::inst();
            //用参数对应POST值
            $params = [];
            foreach ($param_names as $param_name) {
                if(array_key_exists($param_name, $post)){
                    $params[] = $post[$param_name];
                } elseif (array_key_exists($param_name,$param_default_values)) {
                    $params[] = $param_default_values[$param_name];
                } else{
                    ajax_result(false, '缺少参数:'.$param_name);
                }
            }
            $result = call_user_func_array([$class,$func], $params);
            if ($result === false) {
                ajax_result(false, 'error',['data'=>$result]);
            } else {
                ajax_result(true, 'ok',['data'=>$result]);
            }
        } else {

        }
    }

    /**
     * 检查是否有这个类可调用
     * @param $class
     *
     * @return null|\ReflectionClass
     */
    private function checkClass($class) {
        $flag = false;
        $rel_class = null;
        !File::Exists(APP_LIB_PATH.CK_TOP_NS.'/Cms/Controller/'.$class.'.php') or $flag = true;
        if ($flag) {
            $rel_class = new \ReflectionClass($this->namespace.'\\'.$class);
            //获取类注释
            $rel_doc = $rel_class->getDocComment();
            $reg = '/\@res\strue/is';
            if (preg_match($reg, $rel_doc)) {

            } else {
                ajax_result(false,'此模块不能被调用');
            }
        } else {
            ajax_result(false,'没有找到相应模块');
        }
        return $rel_class;
    }

    /**
     * 检查是否有这个方法可调用
     * @param $rel_class \ReflectionClass
     * @param $func
     *
     * @return \ReflectionMethod
     * @throws \Exception
     */
    private function checkFunc($rel_class,$func) {
        $method = null;
        $class = $rel_class->getName();
        if (method_exists($class, $func)) {
            $method = $rel_class->getMethod($func);
            $method_doc = $method->getDocComment();
            $reg = '/\@res\strue/is';
            if (preg_match($reg, $method_doc)) {
                return $method;
            } else {
                ajax_result(false,'此方法不能被调用');
            }
        } else {
            ajax_result(false,'没有找到相应方法');
        }
    }
}