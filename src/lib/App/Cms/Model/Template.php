<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 2017/2/13
 * Time: 14:35
 */

namespace App\Cms\Model;


use CK\Core\Model;
use CK\Database\DBA;

/**
 * 模板模块类
 * Class Template
 *
 * @package App\Admin\Model
 */
class Template extends Model {
    public function __construct() {
        parent::__construct('t_template');
    }

    public function getResult($where=[]){
        $this->_table->where($where)->get_result();
        return DBA::inst()->sql_str;
    }
}