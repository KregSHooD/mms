<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 2017/2/21
 * Time: 11:13
 */

namespace App\Cms\Common;


use App\Cms\Model\Template as TempModel;
use App\Cms\Model\TemplateFunc;
use CK\Core\Component;
use CK\Template\Template;
use CK\Util\Arr;
use CK\Util\IO\File;

class Publish extends Component {
    private $_ns;

    private $_conf = [
        'publish_path'=>APP_PATH,
        'template_conf'=>[
            'path'=>CMS_TEMPLATE_PATH,
            'theme'=>'default',
            'cache_path'=> 'template/cms/',
            'asset_path'=>'/assets/'
        ]
    ];

    private $_tmp;

    private $_template;

    public function __construct($conf = null) {
        if (!$conf) {
            $conf = $this->getConfig();
        }
        if ($conf) {
            $this->_conf = Arr::mrg($this->_conf,$conf);
        }

        $this->_tmp = Template::inst($this->_conf['template_conf']);

        $this->_ns = CK_TOP_NS."\\Cms\\Publish\\";
    }

    /**
     * 加载模板内容
     * @param $tmp_id
     *
     * @return self
     */
    public function load($tmp_id) {
        $this->_tmp->clearAssign();
        $this->_template = null;
        $this->_template = TempModel::inst()->find(['tmp_id'=>$tmp_id]);
        $func_list = TemplateFunc::inst()->find(['tmp_id'=>$tmp_id]);
        $this->explainFunc($func_list);
        return $this;
    }

    /**
     * 把模板内容写入到文件中
     * @param null $publish_path
     *
     * @return int
     */
    public function writeFile($publish_path=null) {
        $content = $this->_tmp->fetch($this->_template['tmp_path']);
        $path = $this->_conf['publish_path'];
        $path .= $publish_path?$publish_path:$this->_template['tmp_publish_path'];
        if ($path[0] === '/') {
            $path = substr($path,1);
        }
        return File::Write($path,$content);
    }

    /**
     * 解释模板方法得到返回的变量,并注入到模板内
     * @param $func_list
     */
    private function explainFunc($func_list) {
        foreach ($func_list as $func) {
            $this->_tmp->assign($func['fun_var'],$this->executeFunc($func));
        }
    }

    /**
     * 执行函数并返回结果
     * @param $func
     *
     * @return mixed
     * @throws \Exception
     */
    private function executeFunc($func) {
        $ref_method = $this->checkFunc($func['fun_class'],$func['fun_function']);
        $relParams = $ref_method->getParameters();
        $param_names = [];
        $param_default_values = [];
        foreach ($relParams as $param) {
            $param_names[] = $param->name;
            //如果有可选参数
            if ($param->isOptional()) {
                $param_default_values[$param->name] = $param->getDefaultValue();
            }
        }

        $param_vars = json_decode($func['fun_params'],true);

        //获取当前实例类
        $class_name = $this->_ns.$func['fun_class'];
        $class = $class_name::inst();
        //用参数对应POST值
        $params = [];
        foreach ($param_names as $param_name) {
            if(array_key_exists($param_name, $param_vars)){
                $params[] = $param_vars[$param_name];
            } elseif (array_key_exists($param_name,$param_default_values)) {
                $params[] = $param_default_values[$param_name];
            } else{
                throw new \Exception('发布函数：类-'.$class_name.',方法-'.$func['fun_function'].','.'缺少参数-'.$param_name);
            }
        }
        $result = call_user_func_array([$class,$func['fun_function']], $params);
        return $result;
    }

    /**
     * 检查是否有这个方法可调用
     * @param string $class_name
     * @param string $func_name
     *
     * @return \ReflectionMethod
     * @throws \Exception
     */
    private function checkFunc($class_name,$func_name) {
        $method = null;
        $func = new \ReflectionMethod($this->_ns.$class_name,$func_name);
        return $func;
    }
}