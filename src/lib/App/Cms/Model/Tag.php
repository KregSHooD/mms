<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/22
 * Time: 17:55
 */

/**
 * CK 开发框架
 * User: Clake
 * Date: 2017/2/20
 * Time: 15:19
 */

namespace App\Cms\Model;


use CK\Core\Model;

class Tag extends Model {
    public function __construct() {
        parent::__construct('t_tag');
    }
}