<?php
namespace App\Cms\Model;
use CK\Core\Model;

/**
 * 友情链接分类
 * User: liberty.lai
 * Date: 2017/2/22
 * Time: 17:45
 */
class LinkCategory extends Model  {
    public function __construct()
    {
        parent::__construct('t_link_category');
    }
}