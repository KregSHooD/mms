<?php
namespace App\Cms\Model;
use CK\Core\Model;

/**
 * 友情链接模型
 * User: liberty.lai
 * Date: 2017/2/22
 * Time: 17:43
 */
class Link extends Model  {
    public function __construct() {
        parent::__construct('t_link');
    }
}