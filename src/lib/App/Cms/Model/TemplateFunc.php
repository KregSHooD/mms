<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 2017/2/15
 * Time: 17:47
 */

namespace App\Cms\Model;


use CK\Core\Model;

class TemplateFunc extends Model {
    public function __construct() {
        parent::__construct('t_template_func');
    }
}