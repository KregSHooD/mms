<?php
/**
 * CK 开发框架
 * User: Clake
 * Date: 2017/2/20
 * Time: 15:19
 */

namespace App\Cms\Model;


use CK\Core\Model;

class ArticleCategory extends Model {
    public function __construct() {
        parent::__construct('t_article_category');
    }
}