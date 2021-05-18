<?php
namespace App\Cms\Model;
/**
 * 友情链接模型
 * User: liberty.lai
 * Date: 2017/2/22
 * Time: 17:43
 */
class Link extends \CK\Core\Model
{
    public function __construct()
    {
        parent::__construct('t_link');
    }
}