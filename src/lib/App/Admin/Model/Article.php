<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 15/6/26
 * Time: 00:49
 */

namespace App\Admin\Model;


use CK\Core\Model;

class Article extends Model{

    public function __construct() {
        parent::__construct('t_article');
    }

}